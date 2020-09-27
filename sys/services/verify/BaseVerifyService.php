<?php
namespace app\modules\sys\services\verify;

use app\modules\common\models\sys\SysVerifyCodeModel;
use app\modules\sys\redis\PhoneCache;
use app\response\ErrorCode;
use app\exceptions\ServiceException;
use app\modules\common\services\BaseService;
use Mobvista\MixTools\Src\Ip\Ip;
use Mobvista\MixTools\Src\Sms\Sms;
use app\modules\common\lib\VerifyCode;
use Mobvista\MixTools\Src\Email\Email;


class BaseVerifyService extends BaseService
{
    //用户注册的模板code
    const SMS_CODE_REGISTER = 'SMS_203195894';
    //忘记密码发送模板code
    const SMS_CODE_RESET_PASSWORD = 'SMS_203195893';

    const PHONE_CODE_COUNT_LIMIT = 20;


    /**
     * 发送邮件验证码
     * @param $type
     * @param $logType
     * @param $email
     * @param $code
     * @param $title
     * @param $content
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    protected function sendEmailCode($type, $logType, $email, $code, $title, $content)
    {

        $createdTime = time() - 60;
        $res = SysVerifyCodeModel::find()
            ->andWhere(['type' => $type])
            ->andWhere(['type_name' => $email])
            ->andWhere(['>=','created_time', $createdTime])
            ->count();
        if ($res > 1) {
            throw new ServiceException('发送失败，请勿频繁操作', ErrorCode::EMAIL_REPEAT);
        }

        $ip = ip2long(Ip::getClientIp());
        $createdTime = time() - 3600;
        $res = SysVerifyCodeModel::find()
            ->andWhere(['type' => $type])
            ->andWhere(['or', ['=', 'ip', $ip], ['=', 'type_name', $email]])
            ->andWhere(['>=','created_time', $createdTime])
            ->count();
        if ($res > 5) {
            throw new ServiceException('发送失败，请勿频繁操作', ErrorCode::EMAIL_REPEAT);
        }

        $data = [
            'type' => $type,
            'type_name' => $email,
            'ip' => $ip,
            'code' => $code,
            'is_verify' => 0,
            'created_time' => time(),
        ];
        $res = SysVerifyCodeModel::insertData($data);
        if ($res) {
            $emailRes = Email::sendMail($email, $title, $content, $logType, 'Mix');

            if (in_array($emailRes,[Email::EMAIL_SEND_STATUS_SUCCESS, Email::EMAIL_SEND_STATUS_RETRY_SUCCESS])) {
                return true;
            } else {
                throw new ServiceException('发送失败，请重试', ErrorCode::EMAIL_FAIL);
            }
        } else {
            throw new ServiceException('发送失败，请重试', ErrorCode::EMAIL_FAIL);
        }
    }


    /**
     * 发送短信验证码
     * @param $type
     * @param $phone
     * @param $templateCode
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    protected function sendSmsCode($type, $phone, $templateCode)
    {
        $createdTime = time() - 60;
        $res = SysVerifyCodeModel::find()
            ->andWhere(['type' => $type])
            ->andWhere(['type_name' => $phone])
            ->andWhere(['>=','created_time', $createdTime])
            ->count();
        if ($res > 1) {
            throw new ServiceException('发送失败，请勿频繁操作', ErrorCode::PHONE_REPEAT);
        }

        $ip = ip2long(Ip::getClientIp());
        $createdTime = time() - 3600;
        $res = SysVerifyCodeModel::find()
            ->andWhere(['type' => $type])
            ->andWhere(['or', "ip = {$ip}", "type_name = {$phone}"])
            ->andWhere(['>=','created_time', $createdTime])
            ->count();
        if ($res > 5) {
            throw new ServiceException('发送失败，请勿频繁操作', ErrorCode::PHONE_REPEAT);
        }

        $code = VerifyCode::randomKeys(4);

        $data = [
            'type' => $type,
            'type_name' => $phone,
            'ip' => $ip,
            'code' => $code,
            'is_verify' => 0,
            'created_time' => time(),
        ];
        $res = SysVerifyCodeModel::insertData($data);
        if ($res) {
            $smsObj = new Sms();
            $smsRes = $smsObj->send($type, ['code' => $code], $phone, $templateCode);
            if ($smsRes) {
                 /** @var PhoneCache $phoneCache */
         $phoneCache = $this->getService(PhoneCache::class);
                $phoneCache->setCountByPhone($phone);
                return true;
            } else {
                throw new ServiceException('发送失败，请重试', ErrorCode::PHONE_FAIL);
            }
        } else {
            throw new ServiceException('发送失败，请重试', ErrorCode::PHONE_FAIL);
        }
    }


    /**
     * 验证code并更新
     * @param $type
     * @param $typeName
     * @param $code
     * @return int
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function verifyCode($type, $typeName, $code)
    {
        $this->checkPhoneCount($type, $typeName);
        $verifyInfo = SysVerifyCodeModel::find()->asArray()
            ->select(['id', 'created_time', 'is_verify', 'code'])
            ->andWhere(['type' => $type])
            ->andWhere(['type_name' => $typeName])
            ->orderBy("id DESC")
            ->limit(1)
            ->one();

        if (empty($verifyInfo)) {
            $this->incrCountByPhone($type, $typeName);
            throw new ServiceException('验证码错误', ErrorCode::VALIDATE_CODE_ERROR);
        } else {
            if ($verifyInfo['is_verify'] != 0 || $verifyInfo['code'] != $code) {
                $this->incrCountByPhone($type, $typeName);
                throw new ServiceException('验证码错误', ErrorCode::VALIDATE_CODE_ERROR);
            }
            $codeTime = time() - 600;
            if ($verifyInfo['created_time'] < $codeTime) {
                $this->incrCountByPhone($type, $typeName);
                throw new ServiceException('验证码已过期', ErrorCode::VALIDATE_CODE_EXPIRED);
            }
        }
        //update sms code
        return SysVerifyCodeModel::find()->createCommand()->update(SysVerifyCodeModel::tableName(),
            ['is_verify' => 1, 'updated_time' => time()], ['id' => $verifyInfo['id']])->execute();
    }

    private function checkPhoneCount(int $type, $typeName)
    {
        if ($type != SysVerifyCodeModel::TYPE_REGISTER_PHONE) {
            return;
        }

         /** @var PhoneCache $phoneCache */
         $phoneCache = $this->getService(PhoneCache::class);
         $phoneCodeCount = $phoneCache->getCountByPhone($typeName);
        if ($phoneCodeCount > self::PHONE_CODE_COUNT_LIMIT) {
            ServiceException::send(ErrorCode::PHONE_CODE_REPEAT);
        }
    }

    private function incrCountByPhone(int $type, $typeName)
    {
        if ($type != SysVerifyCodeModel::TYPE_BIND_PHONE) {
            return;
        }

         /** @var PhoneCache $phoneCache */
         $phoneCache = $this->getService(PhoneCache::class);
        $phoneCache->incrCountByPhone($typeName);
    }
}