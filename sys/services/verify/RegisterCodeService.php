<?php
namespace app\modules\sys\services\verify;

use app\modules\common\models\sys\SysVerifyCodeModel;
use app\response\ErrorCode;
use app\exceptions\ServiceException;
use app\modules\common\enums\LanguageEnum;
use app\modules\common\models\sys\SysUserModel;
use Mobvista\MixTools\Src\Regex\RegexVali;
use app\modules\common\lib\VerifyCode;
use app\modules\common\traits\LanguageTrait;
use app\modules\sys\enums\CompanyIdEnum;
use Mobvista\MixTools\Src\Email\Email;


class RegisterCodeService extends BaseVerifyService
{
    use LanguageTrait;
    private $sysUserModel;
    public function __construct(SysUserModel $sysUserModel)
    {
        $this->sysUserModel = $sysUserModel;
    }
    /**
     * 注册发送邮件
     * @param $email
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function sendRegEmail($email)
    {
        $this->filterRegEmail($email);

        $type = SysVerifyCodeModel::TYPE_REGISTER_EMAIL;
        $code = VerifyCode::randomKeys(4);
        $title = "【丝路】{$code} 是你的注册验证码";

        $content = "欢迎成为丝路的一员！<br>";
        $content .= "你的注册验证码是 {$code}，请在10分钟内使用。该验证码仅用于注册，请勿泄露，如非本人操作，请忽略此邮件。<br>";
        $content .= "<br>";
        $content .= "此致<br>";
        $content .= "<br><br>";
        $content .= "丝路团队";

        return $this->sendEmailCode($type, Email::TYPE_REGISTER, $email, $code, $title, $content);
    }

    /**
     * 验证邮箱的验证码
     * @param $email
     * @param $code
     * @return int
     * @throws ServiceException
     */
    public function verifyRegEmail($email, $code)
    {
        $this->filterRegEmail($email);
        return $this->verifyCode(SysVerifyCodeModel::TYPE_REGISTER_EMAIL, $email, $code);
    }

    /**
     * 注册邮箱的验证条件
     * @param $email
     * @return bool
     * @throws ServiceException
     */
    private function filterRegEmail($email)
    {
        if (!RegexVali::email($email)) {
            throw new ServiceException('请填写正确的邮箱', ErrorCode::EMAIL_ERROR);
        }

        $res = $this->sysUserModel->findByEmail($email);
        if ($res) {
            $language = $this->getLanguage();
            $companyId = $res->company_id;
            if($language == LanguageEnum::CH) {
                $source = $companyId == CompanyIdEnum::SHOPSCANNER ? 'ShopScanner' : '丝路SilkRoad';
                $message = '该账号已在 ' . $source . ' 完成注册，请直接登录';
            } else {
                $source = $companyId == CompanyIdEnum::SHOPSCANNER ?  'ShopScanner' : 'SilkRoad';
                $message = '	The account has been registered in ' . $source . ', please log in directly';
            }
            ServiceException::send(ErrorCode::EMAIL_USED, $message);
        }
        return true;
    }


    /**
     * 注册发送短信
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    public function sendRegPhone($phone)
    {
        $this->filterRegPhone($phone);
        return $this->sendSmsCode(SysVerifyCodeModel::TYPE_REGISTER_PHONE, $phone, self::SMS_CODE_REGISTER);
    }


    /**
     * 注册发送短信
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    public function sendResetPasswordPhone($phone)
    {
        $this->filterResetPasswordPhone($phone);
        return $this->sendSmsCode(SysVerifyCodeModel::TYPE_RESET_PASSWORD_PHONE, $phone, self::SMS_CODE_RESET_PASSWORD);
    }

    /**
     * 验证手机短信验证码注册
     * @param $phone
     * @param $code
     * @return int
     * @throws ServiceException
     */
    public function verifyRegPhone($phone, $code)
    {
        $this->filterRegPhone($phone);
        return $this->verifyCode(SysVerifyCodeModel::TYPE_REGISTER_PHONE, $phone, $code);
    }

    /**
     * 验证手机短信验证码修改密码
     * @param $phone
     * @param $code
     * @return int
     * @throws ServiceException
     */
    public function verifyResetPasswordPhone($phone, $code)
    {
        $this->filterResetPasswordPhone($phone);
        return $this->verifyCode(SysVerifyCodeModel::TYPE_RESET_PASSWORD_PHONE, $phone, $code);
    }

    /**
     * 注册手机的验证条件
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    private function filterRegPhone($phone)
    {
        if (!RegexVali::mobile($phone)) {
            throw new ServiceException('请输入正确的手机号', ErrorCode::PHONE_ERROR);
        }

        $res = SysUserModel::find()
            ->where(['is_del' => 0, 'phone' => $phone])
            ->count();
        if ($res > 0) {
            throw new ServiceException('手机号已经被注册', ErrorCode::PHONE_USED);
        }
        return true;
    }

    /**
     * 重置密码手机的验证条件
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    private function filterResetPasswordPhone($phone)
    {
        if (!RegexVali::mobile($phone)) {
            throw new ServiceException('请输入正确的手机号', ErrorCode::PHONE_ERROR);
        }

        $res = SysUserModel::find()
            ->where(['is_del' => 0, 'phone' => $phone])
            ->count();
        if ($res < 1) {
            throw new ServiceException('手机号不存在', ErrorCode::PHONE_USED);
        }
        return true;
    }
}