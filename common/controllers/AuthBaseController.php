<?php
namespace app\modules\common\controllers;

use app\exceptions\ServiceException;
use app\modules\sys\services\user\PassportService;
use app\modules\sys\services\user\RoleService;
use app\response\ErrorCode;

/**
 * 权限和登录基础控制器
 * Class ProfileController
 * @package app\modules\sys\controllers\user
 */
class AuthBaseController extends BaseController
{

    /**
     * 登录的用户id
     * @var
     */
    protected $userId = 0;

    /**
     * 登录和权限验证
     * @throws \app\exceptions\ServiceException
     */
    public function init()
    {
        parent::init();
        $this->userId = (int)PassportService::getInstance()->authLogin(); //TODO 暂时没有办法，底层调用了上层的代码
        
    }

    public function checkPermission(string $permission)
    {
        /** @var RoleService $roleService */
        $roleService = $this->getService(RoleService::class);
        $authList = $roleService->getAuthListByUserId($this->userId);
        if (!in_array($permission, $authList)) {
            ServiceException::send(ErrorCode::PERMISSION_ERROR);
        }
    }

}