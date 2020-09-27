<?php
namespace app\modules\sys\services\verify;

use app\modules\common\models\sys\SysVerifyCodeModel;
use app\response\ErrorCode;
use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysUserModel;
use Mobvista\MixTools\Src\Regex\RegexVali;
use app\modules\common\lib\VerifyCode;
use Mobvista\MixTools\Src\Email\Email;


class BindCodeService extends BaseVerifyService
{

    /**
     * 绑定邮箱发送邮件
     * @param $userId
     * @param $email
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function sendBindEmail($userId, $email)
    {
        $userInfo = $this->filterBindEmail($userId, $email);

        $type = SysVerifyCodeModel::TYPE_BIND_EMAIL;
        $code = VerifyCode::randomKeys(4);
        $title = "【丝路】{$code} 是你的绑定邮箱验证码";

        $content = "{$userInfo['user_name']}，你好！<br>";
        $content .= "你绑定邮箱的验证码是 {$code}，请在10分钟内使用。该验证码仅用于绑定邮箱，请勿泄露，如非本人操作，请忽略此邮件。<br>";
        $content .= "<br>";
        $content .= "此致<br>";
        $content .= "<br><br>";
        $content .= "丝路团队";

        return $this->sendEmailCode($type, Email::TYPE_UPDATE_EMAIL, $email, $code, $title, $content);
    }


    /**
     * 验证邮箱验证码
     * @param $userId
     * @param $email
     * @param $code
     * @return int
     * @throws ServiceException
     */
    public function verifyBindEmail($userId, $email, $code)
    {
        $this->filterBindEmail($userId, $email);
        return $this->verifyCode(SysVerifyCodeModel::TYPE_BIND_EMAIL, $email, $code);
    }

    /**
     * 绑定邮箱的过滤条件
     * @param $userId
     * @param $email
     * @return bool
     * @throws ServiceException
     */
    private function filterBindEmail($userId, $email)
    {
        if (!RegexVali::email($email)) {
            throw new ServiceException('请填写正确的邮箱', ErrorCode::EMAIL_ERROR);
        }

        $res = SysUserModel::find()
            ->where(['is_del' => 0, 'email' => $email])
            ->count();
        if ($res > 0) {
            throw new ServiceException('邮箱已经被注册', ErrorCode::EMAIL_USED);
        }

        $userInfo = SysUserModel::find()->asArray()
            ->where(['id' => $userId])
            ->one();

        if (!empty($userInfo['email'])) {
            throw new ServiceException('邮箱不允许更改', ErrorCode::EMAIL_NO_UPDATE);
        }
        return $userInfo;
    }

    /**
     * 绑定手机发送短信
     * @param $userId
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    public function sendBindPhone($userId, $phone)
    {
        $this->filterBindPhone($userId, $phone);
        return $this->sendSmsCode(SysVerifyCodeModel::TYPE_BIND_PHONE, $phone, self::SMS_CODE_BIND_PHONE);
    }

    /**
     * 验证绑定手机的验证码
     * @param $userId
     * @param $phone
     * @param $code
     * @return int
     * @throws ServiceException
     */
    public function verifyBindPhone($userId, $phone, $code)
    {
        $this->filterBindPhone($userId, $phone);
        return $this->verifyCode(SysVerifyCodeModel::TYPE_BIND_PHONE, $phone, $code);
    }

    /**
     * 绑定手机的过滤条件
     * @param $userId
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    private function filterBindPhone($userId, $phone)
    {
        if (!RegexVali::mobile($phone)) {
            throw new ServiceException('请输入正确的手机号', ErrorCode::PHONE_ERROR);
        }
        $userRes = SysUserModel::find()
            ->andWhere(['phone' => $phone])
            ->andWhere(['is_del' => 0])
            ->count();

        if ($userRes > 0) {
            throw new ServiceException('手机号已被注册', ErrorCode::PHONE_USED);
        }

        $userInfo = SysUserModel::find()->asArray()
            ->where(['id' => $userId])
            ->one();

        if (!empty($userInfo['phone'])) {
            throw new ServiceException('手机号不允许更改', ErrorCode::PHONE_NO_UPDATE);
        }
        return true;
    }


}