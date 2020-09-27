<?php

namespace app\modules\sys\controllers\user;

use app\modules\sys\services\user\PassportService;
use Mobvista\MixTools\Src\Request\FieldValidate;
use Yii;
use app\modules\common\controllers\BaseController;

/**
 * 用户注册登录功能
 * Class PassportController
 * @package app\modules\common\controllers\user
 */
class PassportController extends BaseController
{
    /**
     * 登录
     *
     * @http POST
     * @params
     * - phone | string | 手机号码 |
     * - password | string | 密码（前端md5加密）
     * - remember | int | 是否记住密码，0、1 | 0
     *
     * @response
     * {"data":{"id":"102","phone":"0"},"code":0,"msg":"Success"}
     *
     * @fields
     * - id | 用户id
     * - phone | 手机号码
     */
    public function actionLogin()
    {
        list($phone, $password) = FieldValidate::validateFields(['phone', 'password'], FieldValidate::METHOD_POST);
        $remember = Yii::$app->request->post('remember', null);
        $res = PassportService::getInstance()->login($phone, $password, $remember);
        $this->output->setData($res);
        return $this->output->getRowsOutput();
    }

    /**
     * 登录
     *
     * @http POST
     * @params
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionLogout()
    {
        PassportService::getInstance()->logout();
        return $this->output->getRowsOutput();
    }
}
