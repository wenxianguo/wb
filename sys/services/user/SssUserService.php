<?php

namespace app\modules\sys\services\user;

use app\modules\common\models\sys\SssUserAccountModel;
use app\modules\common\models\sys\SssUserModel;

class SssUserService
{
    private $sssUserAccountModel;
    private $sssUserModel;
    public function __construct(
        SssUserAccountModel $sssUserAccountModel,
        SssUserModel $sssUserModel
    )
    {
        $this->sssUserAccountModel = $sssUserAccountModel;
        $this->sssUserModel = $sssUserModel;
    }

    public function likeByUsername(string $username, int $page = 1, int $pageSize = 20)
    {
        $offset = ($page - 1) * $pageSize;
        $list = $this->sssUserModel->likeByUsername($username, $offset, $pageSize);
        return $list;
    }

    public function getList(int $page = 1, int $pageSize = 20)
    {
        $offset = ($page - 1) * $pageSize;
        $list = $this->sssUserModel->getList($offset, $pageSize);
        return $list;
    }

    public function findByUserName(string $userName)
    {
        return $this->sssUserAccountModel->findByUsername($userName);
    }

    public function findByTeamAndPlatform(string $team, string $platform, string $field = 'account_id')
    {
        return $this->sssUserAccountModel->findByTeamAndPlatform($team, $platform, $field);
    }
}
