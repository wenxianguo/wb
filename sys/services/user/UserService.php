<?php

namespace app\modules\sys\services\user;

use app\modules\common\models\sys\SysUserModel;
use app\modules\sys\services\ShareService;
use BaseComponents\base\SearchHelper;
use yii\helpers\ArrayHelper;
use app\modules\sys\enums\CompanyIdEnum;
use app\modules\sys\enums\RoleIdEnum;

class UserService
{
    private $sysUserModel;
    private $userExtraService;
    private $shareService;
    public function __construct(
        SysUserModel $sysUserModel, 
        UserExtraService $userExtraService, 
        ShareService $shareService
        )
    {
        $this->sysUserModel = $sysUserModel;
        $this->userExtraService = $userExtraService;
        $this->shareService = $shareService;
    }

    public function findById(int $id)
    {
        return $this->sysUserModel->findById($id);
    }

    public function findExtraById(int $id)
    {
        return $this->userExtraService->findByUserId($id);
    }

    public function findByUsername(string $username)
    {
        return $this->sysUserModel->findByUsername($username);
    }

    public function findByEmail(string $email)
    {
        return $this->sysUserModel->findByEmail($email);
    }

    public function findByPhone(int $phone)
    {
        return $this->sysUserModel->findByPhone($phone);
    }

    public function likeByUsername(string $username, array $field = [])
    {
        return $this->sysUserModel->likeByUsername($username, $field);
    }

    public function getIdByUsername(string $username)
    {
        $users = $this->likeByUsername($username, ['id']);
        if (!$users) {
            return [];
        }
        $userIds = array_column($users, 'id');
        return $userIds;
    }

    /**
     * 是否是超级管理员
     * @param int $userId
     * @return bool
     */
    public function isAdmin(int $userId)
    {
        $user = $this->sysUserModel->findById($userId);
        if (!$user) {
            return false;
        }
        $isAdmin = false;
        if ($user->company_id == CompanyIdEnum::MOBVISTA && $user->role_id == RoleIdEnum::MOBVISTA_ADMIN) {
            $isAdmin = true;
        }
        return $isAdmin;
    }

    public function update(SysUserModel $sysUserModel, array $data)
    {
        $this->sysUserModel->updateSysUser($sysUserModel, $data);
    }

    public function updateExtra(int $userId, array $data)
    {
        $data['user_id'] = $userId;
        $this->userExtraService->update($userId, $data);
    }

    public function create(array $data)
    {
        return $this->sysUserModel->create($data);
    }

    public function updateRoleByUserIds(array $userIds, int $companyId, int $roleId)
    {
        $data = [
            'company_id' => $companyId,
            'role_id' => $roleId
        ];
        foreach ($userIds as $userId) {
            $user = $this->sysUserModel->findById($userId);
            if ($user) {
                $this->sysUserModel->updateSysUser($user, $data);
            }
        }
    }

    /**
     * 更新密码
     * @param int $userId
     * @param $password
     * @return array|\yii\db\ActiveRecord|null
     */
    public function updatePasswordByUserId(int $userId, $password)
    {
        $user = $this->sysUserModel->findById($userId);
        if ($user) {
            $password = md5($password);
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            $this->sysUserModel->updateSysUser($user, $data);
        }
        return $user;
    }

    public function getList(array $searchList, int $page = 1, int $pageSize = 20)
    {
        $offset = ($page - 1) * $pageSize;
        $list = $this->sysUserModel->findBySearchList($searchList, $offset, $pageSize);
        return $list;
    }

    public function total(array $searchList)
    {
        $total = $this->sysUserModel->totalBySearchList($searchList);
        return $total;
    }

    public function filterSearchList(string $search)
    {
        $searchList = (new SearchHelper())->parseSearchToWhere($search);
        $where = ['and'];
        foreach ($searchList as $search) {
            $item = $search['item'];
            $val = $search['val'];
            switch ($item) {
                case 'sss_user_name':
                    $extras = $this->userExtraService->likeBySssUserName($val);
                    $userIds = array_column(ArrayHelper::toArray($extras), 'user_id');
                    $where[] = ['in', 'id', $userIds];
                    break;
                case 'promoter':
                    $shares = $this->shareService->likeByPromoter($val);
                    $shareIds = array_column(ArrayHelper::toArray($shares), 'id');
                    $userExtras = $this->userExtraService->findByShareIds($shareIds);
                    $userIds = array_column(ArrayHelper::toArray($userExtras), 'user_id');
                    $where[] = ['in', 'id', $userIds];
                    break;
                default:
                    $where[] = [$search['op'], $item, $val];
            }
        }
        return $where;
    }
}
