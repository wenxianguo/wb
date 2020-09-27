<?php
namespace app\modules\sys\services\verify;

use app\modules\common\lib\VerifyCode;
use app\modules\common\models\sys\SysVerifyCodeModel;
use app\response\ErrorCode;
use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysUserModel;
use Mobvista\MixTools\Src\Email\Email;
use Mobvista\MixTools\Src\Ip\Ip;
use Mobvista\MixTools\Src\Regex\RegexVali;


class ResetPassCodeService extends BaseVerifyService
{

    /**
     * 忘记密码发送短信
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
     * 忘记密码验证短信
     * @param $phone
     * @param $code
     * @return array
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function verifyResetPasswordPhone($phone, $code)
    {
        $this->filterResetPasswordPhone($phone);
        $res = $this->verifyCode(SysVerifyCodeModel::TYPE_RESET_PASSWORD_PHONE, $phone, $code);
        if ($res) {
            //插入reset password page token`
            $ip = ip2long(Ip::getClientIp());
            $token = VerifyCode::randomKeys(8);
            $data = [
                'type' => SysVerifyCodeModel::TYPE_RESET_PASSWORD_PHONE_PAGE,
                'type_name' => $phone,
                'ip' => $ip,
                'code' => $token,
                'is_verify' => 0,
                'created_time' => time(),
            ];
            SysVerifyCodeModel::insertData($data);
            return ['token' => $token];

        } else {
            throw new ServiceException('系统错误');
        }
    }

    /**
     * 忘记密码发送手机的过滤条件
     * @param $phone
     * @return bool
     * @throws ServiceException
     */
    private function filterResetPasswordPhone($phone)
    {
        if (!RegexVali::mobile($phone)) {
            throw new ServiceException('请输入正确的手机号', ErrorCode::PHONE_ERROR);
        }
        $userRes = SysUserModel::find()
            ->andWhere(['phone' => $phone])
            ->andWhere(['is_del' => 0])
            ->count();

        if ($userRes < 1) {
            throw new ServiceException('此手机号未注册', ErrorCode::PHONE_NOT_USED);
        }
        return true;
    }


    /**
     * 发送重置密码邮件
     * @param $email
     * @return void
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function sendResetPasswordEmail($email)
    {
        $userInfo = $this->filterResetPasswordEmail($email);

        $type = SysVerifyCodeModel::TYPE_RESET_PASSWORD_EMAIL_PAGE;


        $token = VerifyCode::randomStr(20);
        $title = '【丝路】重置密码链接';
        $url =  SLR_URL . "/password_reset?token={$token}";
        $content = "{$userInfo['user_name']}，你好！<br>";
        $content .= "我们收到你的重置密码请求，请<a href='{$url}'>点击此处更改密码</a>。<br>";
        $content .= "链接有效期为24小时，请勿泄露，如未做任何操作，系统将保留原密码；如非本人操作，请忽略此邮件。<br>";
        $content .= "<br>";
        $content .= "此致<br>";
        $content .= "<br><br>";
        $content .= "丝路团队";

        $this->sendEmailCode($type, Email::TYPE_RESET_PASSWORD, $email, $token, $title, $content);
    }


    /**
     * 邮箱的验证条件
     * @param $email
     * @return bool
     * @throws ServiceException
     */
    private function filterResetPasswordEmail($email)
    {
        if (!RegexVali::email($email)) {
            throw new ServiceException('请填写正确的邮箱', ErrorCode::EMAIL_ERROR);
        }

        $res = SysUserModel::find()->asArray()
            ->where(['is_del' => 0, 'email' => $email])
            ->one();
        if (empty($res)) {
            throw new ServiceException('此邮箱未注册', ErrorCode::EMAIL_NOT_USED);
        }
        return $res;
    }



}