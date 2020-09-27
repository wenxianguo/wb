<?php

namespace app\modules\sys\services\user;

use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysCompanyGroupModel;
use app\modules\common\models\sys\SysRoleModel;
use app\modules\common\models\sys\SysUserModel;
use app\modules\common\services\BaseService;
use app\response\ErrorCode;
use app\modules\common\services\OptLogService;

class GroupService extends BaseService
{
    /**
     * 根据user获取组
     *
     * @param $companyId
     * @param $page
     * @param $pageSize
     * @param $total
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getGroupListByUser($companyId, $page, $pageSize, &$total)
    {
        $res = [];
        $query = SysCompanyGroupModel::find()->where(['company_id' => $companyId, 'is_del' => 0]);
        //计算总数
        if (!is_null($total)) {
            $total = $query->count();
            if ($total == 0) {
                return $res;
            }
        }

        $offset = intval(($page - 1) * $pageSize);
        $res = $query->asArray()
            ->select(['id', 'group_name'])
            ->orderBy("id DESC")
            ->offset($offset)
            ->limit($pageSize)
            ->all();

        return $res;
    }

    /**
     * 增加分组
     *
     * @param array $data
     * @return int|string
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function addGroup(array $data)
    {
        $data['created_time'] = time();
        $result = SysCompanyGroupModel::insertData($data);
        OptLogService::getInstance()->setCond('app\modules\common\models\sys\SysCompanyGroupModel', 'created')
            ->setCreateNewValue($data)
            ->log();
        return $result;
    }

    /**
     * 更新分组
     *
     * @param $groupId
     * @param $companyId
     * @param array $data
     * @return mixed
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function updateGroup($groupId, $companyId, array $data)
    {
        $where = ['id' => $groupId, 'company_id' => $companyId];
        list($oldUpdateContent, $newUpdateContent, $result) = SysCompanyGroupModel::updateInfo(['group_name' => $data['group_name'], 'updated_time' => time()], $where);
        OptLogService::getInstance()->setCond('app\modules\common\models\sys\SysCompanyGroupModel', 'updated', $where)
            ->setUpdateOldValue($oldUpdateContent)
            ->setUpdateNewValue($newUpdateContent)
            ->log();
        return $result;
    }

    /**
     * 删除分组
     *
     * @param $groupId
     * @param $companyId
     * @return array|bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function delGroup($groupId, $companyId)
    {
        $check = SysUserModel::find()->where(['company_id' => $companyId, 'group_id' => $groupId, 'is_del' => 0])->count();
        if ($check) {
            throw new ServiceException('小组下还有成员，请先移除小组的成员', ErrorCode::GROUP_DEL_USER_EXISTS);
        }

        $where = ['id' => $groupId, 'company_id' => $companyId];
        list($_, $_, $result) = SysCompanyGroupModel::updateInfo(['is_del' => 1, 'updated_time' => time()], $where);
        OptLogService::getInstance()->setCond('app\modules\common\models\sys\SysCompanyGroupModel', 'deleted', $where)
            ->setDeleteOldValue()
            ->log();
        return $result;
    }


    /**
     * 获取小组用户列表
     *
     * @param $filter
     * @param $page
     * @param $pageSize
     * @param $total
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUserListByGroup($filter, $page, $pageSize, &$total)
    {
        $companyId = $filter['company_id'];
        $groupId = $filter['group_id'];
        $keyword = $filter['keyword'];

        $list = [];
        $query = SysUserModel::find()->where(['company_id' => $companyId, 'group_id' => $groupId, 'is_del' => 0]);
        if (!empty($keyword)) {
            $query->andWhere(['like', 'user_name', $keyword]);
        }

        if (!is_null($total)) {
            $total = $query->count();
            if ($total == 0) {
                return $list;
            }
        }

        $offset = intval(($page - 1) * $pageSize);
        $list = $query->asArray()
            ->select(['id', 'user_name', 'email', 'phone', 'group_id', 'role_id'])
            ->orderBy("id DESC")
            ->offset($offset)
            ->limit($pageSize)
            ->all();
        if (!empty($list)) {
            $roleIdArr = array_unique(array_column($list, 'role_id'));
            $roleList = SysRoleModel::find()->asArray()
                ->where("id in (" . implode(",", $roleIdArr) . ")")
                ->andWhere(['company_id' => $companyId])
                ->all();
            $roleArr = !empty($roleList) ? array_column($roleList, 'role_name', 'id') : [];
            foreach ($list as &$listItem) {
                $listItem['role_name'] = isset($roleArr[$listItem['role_id']]) ? $roleArr[$listItem['role_id']] : '';
            }

        }

        return $list;
    }
}
