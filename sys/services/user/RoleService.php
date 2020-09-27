<?php

namespace app\modules\sys\services\user;

use app\modules\common\services\BaseService;
use app\modules\sys\enums\CompanyIdEnum;
use app\modules\sys\enums\RoleIdEnum;
use app\modules\sys\enums\SysGlobalValKeyEnum;
use app\modules\sys\services\globalval\GlobalValService;

class RoleService extends BaseService
{
    private $globalValService;
    private $userService;
    public function __construct(GlobalValService $globalValService, UserService $userService)
    {
        $this->globalValService = $globalValService;
        $this->userService = $userService;
    }
    public function getAuthListByUserId(int $userId)
    {
        $user = $this->userService->findById($userId);
        return $this->getAuthList((int)$user->company_id, (int)$user->role_id);
    }

    public function getAuthList(int $companyId, int $roleId)
    {
        $roles = $this->globalValService->getByKey(SysGlobalValKeyEnum::ROLE);

        switch (true) {
            case in_array($companyId, [CompanyIdEnum::DEFAULT, CompanyIdEnum::SHOPLAZZA, CompanyIdEnum::SHOPIFY]);
                $authList = $roles['default'];
                break;
            case $companyId == CompanyIdEnum::MOBVISTA && $roleId == RoleIdEnum::MOBVISTA_OPERATE;
                $authList = $roles['mobvista_operate'];
                break;
            case $companyId == CompanyIdEnum::MOBVISTA && $roleId == RoleIdEnum::MOBVISTA_OPTIMIZER:
                $authList = $roles['mobvista_optimizer'];
                break;
            case $companyId == CompanyIdEnum::MOBVISTA && $roleId == RoleIdEnum::MOBVISTA_ADMIN:
                $authList = $roles['mobvista_admin'];
                break;
            default:
                $authList = $roles['default'];
        }
        return $authList;
    }
}
