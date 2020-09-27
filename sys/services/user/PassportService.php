<?php

namespace app\modules\sys\services\user;

use app\modules\common\lib\ElkLogType;
use app\modules\common\models\sys\SysVerifyCodeModel;
use app\modules\sys\enums\SysGlobalValKeyEnum;
use app\modules\sys\redis\UserCache;
use app\modules\sys\services\globalval\GlobalValService;
use app\modules\sys\services\verify\PageCodeService;
use app\response\ErrorCode;
use app\exceptions\ServiceException;
use app\modules\common\assemble\sys\UserAssemble;
use app\modules\common\models\sys\SysUserModel;
use app\modules\common\services\BaseService;
use Mobvista\MixTools\Src\Domain\Domain;
use Mobvista\MixTools\Src\Elk\Elk;
use Mobvista\MixTools\Src\Ip\Ip;
use Mobvista\MixTools\Src\Regex\RegexVali;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class PassportService extends BaseService
{
    /** @var UserService $userService */
    private $userService;
    public function __construct()
    {
        $this->userService = $this->getService(UserService::class);
    }

    /**
     * 存放登录session的名称
     */
    const SESSION_USER_ID = 'slr_user_id';

    /**
     * 登录保存cookie的加密值
     */
    const LOGIN_COOKIE_SALT = 'af1u2c3k';

    /**
     * 注册发送短信
     *
     * @param $data
     * @param bool $isLogin
     * @return array
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function register($data, $isLogin = true)
    {
        $phone = $data['phone'] ?? 0;
        $companyId = $data['company_id'] ?? 1;
        $roleId = $data['role_id'] ?? 1;

        $data = [
            'user_name' => $data['user_name'],
            'phone' => $phone,
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),  //前端把密码md5了
            'last_login_time' => time(),
            'last_login_ip' => ip2long(Ip::getClientIp()),
            'created_time' => time(),
            'company_id' => $companyId,
            'role_id' => $roleId
        ];
        $res = SysUserModel::insertData($data);
        if ($res && $isLogin) {
            $this->setLoginSession($res);
            return $this->getLoginInfo();
        } elseif ($res) {
            return $res;
        } else {
            throw new ServiceException('系统错误');
        }
    }

    public function loginByUserId(int $userId)
    {
        $userInfo = $this->userService->findById($userId);
        $data = ['last_login_ip' => ip2long(Ip::getClientIp()), 'last_login_time' => time(), 'updated_time' => time()];
        $this->userService->update($userInfo, $data);
        $this->setLoginSession($userId);
        $data['PHPSESSID'] = session_id();
        $data['location'] = self::class;
        $data['user_id'] = $userId;
        Elk::log(ElkLogType::USER_LOGIN_INFO, var_export($data, true), Elk::LEVEL_INFO);
        return $this->getLoginInfo();
    }

    public function loginByUsername(string $username)
    {
        $user = $this->findByUsername($username);
        if ($user) {
            $this->loginByUserId($user->id);
        }
    }

    public function findByUsername(string $username)
    {
        $user = $this->userService->findByUsername($username);
        return $user;
    }

    /**
     * 登录
     *
     * @param $phone
     * @param $password
     * @param $remember
     * @return array
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function login($phone, $password, $remember)
    {
        if (!Yii::$app->session->has(self::SESSION_USER_ID)) {
            $userInfo = $this->userService->findByPhone($phone);

            if (empty($userInfo)) {
                throw new ServiceException('账号不存在', ErrorCode::VALIDATE_LOGIN_NAME_ERROR);
            }
            
            if (!password_verify($password, $userInfo['password'])) {
                throw new ServiceException('密码错误', ErrorCode::VALIDATE_PASSWORD_ERROR);
            }

            // 更新登录信息
            $data = ['last_login_ip' => ip2long(Ip::getClientIp()), 'last_login_time' => time(), 'updated_time' => time()];
            $this->userService->update($userInfo, $data);
            $this->setLoginSession($userInfo['id']);
            $data['PHPSESSID'] = session_id();
            $data['user_id'] = $userInfo['id'];
            Elk::log(ElkLogType::USER_LOGIN_INFO, var_export($data, true), Elk::LEVEL_INFO);



            //设置cookie记住密码 7天内有效
            $day = $remember ? 7 : 1;
            $cookie = new \yii\web\Cookie();
            $cookie->name = $this->getLoginCookieName();
            $cookieInfo = ['id' => $userInfo['id'], 'token' => $this->getLoginCookieToken($userInfo['id'])];
            $cookie->value = base64_encode(json_encode($cookieInfo));           //cookie值
            Elk::log(ElkLogType::USER_LOGIN_INFO, var_export($cookieInfo, true), Elk::LEVEL_INFO);
            $cookie->expire = time() + $day * 86400;
            $cookie->path = '/';
            $cookie->domain = Domain::getRootDomainFromDomain();
            Yii::$app->response->cookies->add($cookie);
        }
        return $this->getLoginInfo();
    }

    /**
     * 获取登录信息
     *
     * @return array
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws ServiceException
     */
    public function getLoginInfo()
    {
        $sessionObj = Yii::$app->session;
        if ($sessionObj->has(self::SESSION_USER_ID)) {
            $session = (int)$sessionObj->get(self::SESSION_USER_ID);
            //模拟登录信息
            $session = $this->simulationLogin($session);
            $userInfo = $this->userService->findById($session);
            if (empty($userInfo)) {
                throw new ServiceException('请先进行登录', ErrorCode::ERROR_NOT_LOGIN);
            }
            $this->accessRecord($session);
            $field = '{id,phone}';
            $info = $this->assemble(UserAssemble::class, $userInfo, $field);
            return $info;
        } else {
            throw new ServiceException('找不到用户', ErrorCode::USER_NOT_EXIST);
        }
    }

    /**
     * 登录判断
     *
     * @return mixed
     * @throws ServiceException
     */
    public function authLogin()
    {
        $sessionObj = Yii::$app->session;
        $cookieObj = Yii::$app->request->cookies;

        //优先判断cookie的记住密码
        if (!$sessionObj->has(self::SESSION_USER_ID)) {
            $loginCookieName = $this->getLoginCookieName();
            if ($cookieObj->has($loginCookieName)) {
                $loginCookie = $cookieObj->get($loginCookieName);
                $cookieToken = base64_decode($loginCookie);
                $cookieToken = !empty($cookieToken) ? json_decode($cookieToken, true) : null;
                if (!empty($cookieToken['id']) && !empty($cookieToken['token'])) {

                    if ($this->getLoginCookieToken($cookieToken['id']) == $cookieToken['token']) {
                        //设置session
                        $this->setLoginSession($cookieToken['id']);
                        $userId = $this->simulationLogin($cookieToken['id']);
                        return $userId;
                    }
                }
            }
            $data['PHPSESSID'] = session_id();
            $data['location'] = self::class;
            Elk::log(ElkLogType::USER_LOGIN_ERROR_INFO, var_export($data, true), Elk::LEVEL_ERROR);
            throw new ServiceException('请先进行登录', ErrorCode::ERROR_NOT_LOGIN);
        } else {
            //有session
            $userId = (int)$sessionObj->get(self::SESSION_USER_ID);
            //模拟登录用户
            $userId = $this->simulationLogin($userId);
            return $userId;
        }
    }

    /**
     * 注销登录
     *
     * @return bool
     */
    public function logout()
    {
        $sessionObj = Yii::$app->session;

        //删除session
        if ($sessionObj->has(self::SESSION_USER_ID)) {
            $sessionObj->remove(self::SESSION_USER_ID);
        }
        //删除cookie
        $loginCookieName = $this->getLoginCookieName();
        if (Yii::$app->request->cookies->has($loginCookieName)) {
            $cookie = new yii\web\Cookie();
            $cookie->domain = Domain::getRootDomainFromDomain();
            $cookie->name = $this->getLoginCookieName();
            Yii::$app->response->cookies->remove($cookie);
        }
        return true;
    }

    /**
     * 获取加密后的登录cookie值
     *
     * @param $userId
     * @return string
     */
    private function getLoginCookieToken($userId)
    {
        return md5(trim($userId) . trim(self::LOGIN_COOKIE_SALT));
    }

    /**
     * 获取登录保持的cookie
     *
     * @return string
     */
    private function getLoginCookieName()
    {
        return Yii::$app->params['cookie_prefix'] . 'auth_token_'  . Yii::$app->params['environment'];
    }

    /**
     * 设置登录session
     *
     * @param $userId
     * @return mixed
     */
    public function setLoginSession($userId)
    {
        return Yii::$app->session->set(self::SESSION_USER_ID, $userId);
    }

    /**
     * 获取登录session
     *
     * @return mixed
     */
    public function getLoginSession()
    {
        return (int)Yii::$app->session->get(self::SESSION_USER_ID);
    }

    /**
     * 判断是否有session
     *
     * @param $userId
     * @return mixed
     */
    public function hasSession()
    {
        return Yii::$app->session->has(self::SESSION_USER_ID);
    }

    /**
     * 重置密码
     *
     * @param $token
     * @param $typeStr
     * @param $password
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function resetPassword($token, $typeStr, $password)
    {
        $info = PageCodeService::getInstance()->getPageStatus($token, $typeStr, true);

        $updateData = [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'updated_time' => time(),
        ];
        if ($info['type'] == SysVerifyCodeModel::TYPE_RESET_PASSWORD_PHONE_PAGE) {
            $userInfo = $this->userService->findByPhone($info['type_name']);
        } elseif ($info['type'] == SysVerifyCodeModel::TYPE_RESET_PASSWORD_EMAIL_PAGE) {
            $userInfo = $this->userService->findByPhone($info['type_name']);
        }

        if (!empty($userInfo)) {
            $this->userService->update($userInfo, $updateData);
            return true;
        } else {
            throw new ServiceException('页面已过期', ErrorCode::USER_RESET_PASSWORD_ERROR);
        }

    }

    public function updatePassword(string $loginName, string $oldPassword, string $newPassword)
    {
        $userInfo = $this->userService->findByEmail($loginName);
        if (empty($userInfo)) {
            throw new ServiceException('密码错误', ErrorCode::VALIDATE_PASSWORD_ERROR);
        }

        // 前端把密码md5了
        if (!password_verify($oldPassword, $userInfo->password)) {
            throw new ServiceException('密码错误', ErrorCode::VALIDATE_PASSWORD_ERROR);
        }

        $data = [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'updated_time' => time()
        ];
        $this->userService->update($userInfo, $data);
    }

    public function forgetPassword(string $loginName, string $password)
    {
        $userInfo = $this->userService->findByEmail($loginName);
        if (empty($userInfo)) {
            throw new ServiceException('用户不存在', ErrorCode::VALIDATE_PASSWORD_ERROR);
        }

        $data = [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'updated_time' => time()
        ];
        $this->userService->update($userInfo, $data);
    }

    /**
     * 访问记录
     * @param int $userId
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    private function accessRecord(int $userId)
    {
        if (empty($userId)) {
            return;
        }
        /** @var UserCache $userCache */
        $userCache = $this->getService(UserCache::class);
        $userCache->setBitByUserId($userId);
    }

    /**
     * 模拟用户登录
     * @param int $userId
     * @return int
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function simulationLogin(int $userId)
    {
        return (int)$userId;
    }
}