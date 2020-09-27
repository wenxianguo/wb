<?php

namespace app\modules\sys\controllers\user;

use app\exceptions\ServiceException;
use app\modules\sys\services\user\RegisterService;
use app\response\ErrorCode;
use app\modules\common\controllers\BaseController;
use app\modules\sys\services\verify\RegisterCodeService;
use yii\helpers\ArrayHelper;

/**
 * 用户注册功能
 * Class PassportController
 * @package app\modules\common\controllers\user
 */
class RegisterController extends BaseController
{
    private $registerService;
    private $registerCodeService;
    public function __construct($id, $module, RegisterService $registerService, RegisterCodeService $registerCodeService, $config = [])
    {
        $this->registerService = $registerService;
        $this->registerCodeService = $registerCodeService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 用户注册
     *
     * @http POST
     * @params
     * - phone | string | 手机号码 | | Y
     * - password | string | 密码（前端md5加密）
     * - code | string | 验证码
     *
     * @response
     * {"data":{"id":"102","phone":"0"},"code":0,"msg":"Success"}
     *
     * @fields
     * - id | 用户id
     * - user_name | 用户名
     * - email | 邮箱
     * - phone | 手机号码
     */
    public function actionRegister()
    {
        $phone = $this->post('phone', '');
        $password = $this->post('password', '', 'trim');
        $code = $this->post('code', '');

        $this->checkParam($phone, $password, $code);

        $userInfo = $this->registerService->register($phone, $password, $code);
        $data = ArrayHelper::toArray($userInfo);
        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }

    /**
     * 发送注册手机验证码
     *
     * @http POST
     * @params
     * - phone | string | 手机号码 | | Y
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionSendRegPhone()
    {
        $email = $this->post('phone', '');
        $this->registerCodeService->sendRegPhone($email);

        return $this->output->getRowsOutput();
    }

    
    /**
     * 发送重置密码手机验证码
     *
     * @http POST
     * @params
     * - phone | string | 手机号码 | | Y
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionSendResetPasswordPhone()
    {
        $phone = $this->post('phone', '', 'trim');
        $this->registerCodeService->sendResetPasswordPhone($phone);

        return $this->output->getRowsOutput();
    }

    /**
     * 通过手机号码重置密码
     *
     * @http POST
     * @params
     * - phone | string | 手机号码 | | Y
     * - password | string | 密码 | | Y
     * - code | string | 验证码 | | Y
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionResetPassword()
    {
        $phone = $this->post('phone', '');
        $code = $this->post('code', '');
        $password = $this->post('password', '');
        if (!$code) {
            ServiceException::send(ErrorCode::CODE_IS_EMPTY, '验证码不能为空');
        }

        if (!$password) {
            ServiceException::send(ErrorCode::PASSWORD_IS_EMPTY);
        }

        $this->registerService->resetPassword($phone, $code, $password);

        return $this->output->getRowsOutput();
    }

    private function checkParam(string $phone, string $password, string $code)
    {
        if (!$phone) {
            ServiceException::send(ErrorCode::PHONE_IS_EMPTY);
        }

        if (!$password) {
            ServiceException::send(ErrorCode::PASSWORD_IS_EMPTY);
        }

        if (!$code) {
            ServiceException::send(ErrorCode::CODE_IS_EMPTY);
        }
    }
}
