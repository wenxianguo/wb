<?php

namespace app\modules\sys\services\user;

use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysAuthModel;
use app\modules\common\models\sys\SysCompanyAuthModel;
use app\modules\common\models\sys\SysCompanyModel;
use app\modules\common\services\BaseService;
use app\modules\common\services\OptLogService;

class CompanyService extends BaseService
{
    /**
     * 获取企业信息
     * @param $companyId
     * @param array $fields
     * @param bool $getAuth
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getCompanyInfo($companyId, array $fields, $getAuth = true)
    {
        $companyInfo = SysCompanyModel::getInfo($fields, ['id' => $companyId]);
        if (!empty($companyInfo)) {
            if ($getAuth) {
                //公司权限
                $joinWhere = '`' . SysAuthModel::tableName() . '`.`id` = `' . SysCompanyAuthModel::tableName() . '`.`auth_id`';
                $fields = [
                    "`" . SysAuthModel::tableName() . "`.`auth_key`",
                ];
                $where = [
                    "and",
                    "`" . SysCompanyAuthModel::tableName() . "`.`company_id` = {$companyId}",
                    "`" . SysAuthModel::tableName() . "`.`parent_id` = 0",
                    "`" . SysAuthModel::tableName() . "`.`is_del` = 0",
                ];
                $query = SysCompanyAuthModel::find();
                $authList = $query->asArray()
                    ->select($fields)
                    ->where($where)
                    ->innerJoin(SysAuthModel::tableName(), $joinWhere)
                    ->orderBy("`" . SysAuthModel::tableName() . "`.`id` desc")
                    ->all();
                //var_dump($query->createCommand()->getRawSql());die;
                $companyInfo['auth_list'] = !empty($authList) ? array_column($authList, 'auth_key') : [];
            }
            return $companyInfo;
        }
        return [];
    }

    /**
     * 保存公司
     *
     * @param $companyId
     * @param array $data
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function saveCompany($companyId, array $data)
    {
        $updateData = [];
        $updateFields = [
            'industry_key', 'scope_key', 'address'
        ];
        foreach ($updateFields as $dataItem) {
            if (isset($data[$dataItem]) && $data[$dataItem] !== null) {
                $updateData[$dataItem] = trim($data[$dataItem]);
            }
        }
        if (!empty($updateData)) {
            $updateData['updated_time'] = time();
            list($oldUpdateContent, $newUpdateContent, $result) = SysCompanyModel::updateInfo($updateData, ['id' => $companyId]);
            OptLogService::getInstance()->setCond('app\modules\common\models\sys\SysCompanyModel', 'updated', ['id' => $companyId])
                ->setUpdateOldValue($oldUpdateContent)
                ->setUpdateNewValue($newUpdateContent)
                ->log();
            return $result;
        } else {
            throw new ServiceException('参数错误');
        }
    }
}