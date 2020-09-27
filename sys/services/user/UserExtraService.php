<?php

namespace app\modules\sys\services\user;

use app\modules\common\models\sys\SysUserExtraModel;
use app\modules\sys\redis\AccountCache;

class UserExtraService
{
    private $sysUserExtraModel;
    private $accountCache;
    public function __construct(SysUserExtraModel $sysUserExtraModel, AccountCache $accountCache)
    {
        $this->sysUserExtraModel = $sysUserExtraModel;
        $this->accountCache = $accountCache;
    }

    public function update(int $userId, array $data)
    {
        $userExtra = $this->findByUserId($userId);
        if ($userExtra) {
            $this->sysUserExtraModel->updateModel($userExtra, $data);
        } else {
            $this->sysUserExtraModel->create($data);
        }
        if (isset($data['sss_user_name'])) {
            $this->accountCache->syncAccount($userId, $data['sss_user_name']);
        }
    }

    public function create(array $data)
    {
        $this->sysUserExtraModel->create($data);
    }

    public function findByUserId(int $userId)
    {
        return $this->sysUserExtraModel->findByUserId($userId);
    }

    public function findByShareIds(array $shareIds)
    {
        return $this->sysUserExtraModel->findShareIds($shareIds);
    }

    public function likeBySssUserName(string $sssUserName)
    {
        return $this->sysUserExtraModel->likeBySssUserName($sssUserName);
    }
}
