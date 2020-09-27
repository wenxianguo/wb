<?php
namespace app\modules\sys\controllers\user;

use app\modules\sys\services\user\PassportService;
use app\modules\common\controllers\AuthBaseController;
use app\modules\sys\services\user\ProfileService;

/**
 * 个人资料
 * Class ProfileController
 * @package app\modules\common\controllers\user
 */
class ProfileController extends AuthBaseController
{
    /**
     * 用户信息
     *
     * @http POST
     * @params
     *
     * @response
     * {"data":{"id":"102","phone":"0"},"code":0,"msg":"Success"}
     *
     * @fields
     * - id | 用户id
     * - phone | 手机号码
     */
    public function actionInfo()
    {
        $res = PassportService::getInstance()->getLoginInfo();
        $this->output->setData($res);
        return $this->output->getRowsOutput();
    }

    /**
     * 修改密码
     *
     * @http POST
     * @params
     * - new_password | string | 新密码，前端传过来是md5加密的值
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     */
    public function actionUpdatePassword()
    {
        
        $newPassword = $this->post('new_password', '', 'trim');
        ProfileService::getInstance()->updatePassword($this->userId, $newPassword);
        return $this->output->getRowsOutput();
    }
}