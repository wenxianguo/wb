<?php

namespace app\modules\sys\services\optLog;

use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysUserModel;
use app\modules\common\services\BaseService;
use app\modules\common\models\sys\SysLogOptModel;
use app\modules\sys\services\user\PassportService;
use app\modules\common\models\sys\SysGlobalVarModel;
use yii\helpers\Json;

class OptLogListService extends BaseService
{
    /**
     * 获取日志列表
     *
     * @param $where
     * @param $page
     * @param $pageSize
     * @return array|\yii\db\ActiveRecord[]
     * @throws \app\exceptions\ServiceException
     */
    public function getList($where, $page, $pageSize)
    {
        // 查找数据
        $query = $this->renderQuery($where);

        $list = $query->select('page, operation, sys_user_id as operator, created_time')
            ->orderBy('id desc')
            ->offset(intval(($page - 1) * $pageSize))
            ->limit($pageSize)
            ->asArray()
            ->all();

        $content = [];
        if (!empty($list)) {
            // 获取user数据进行转换
            $sysUserInfo = SysUserModel::find()
                ->select('user_name, id')
                ->where(['in', 'id', array_column($list, 'operator')])
                ->asArray()
                ->all();
            $sysUserMap = array_column($sysUserInfo, 'user_name', 'id');
            foreach ($list as $key => $value) {
                $content[] = [
                    'page' => $value['page'],
                    'operation' => $value['operation'],
                    'operator' => $sysUserMap[$value['operator']],
                    'created_time' => date('Y-m-d H:i:s', $value['created_time'])
                ];
            }
        }

        return $content;
    }

    /**
     * 获取总数
     *
     * @param $where
     * @return int|string
     * @throws \app\exceptions\ServiceException
     */
    public function getTotal($where)
    {
        $query = $this->renderQuery($where);
        return $query->count();
    }

    /**
     * 构造sql查询
     *
     * @param $where
     * @return \yii\db\ActiveQuery
     * @throws \app\exceptions\ServiceException
     */
    private function renderQuery($where)
    {
        $sysUserInfo = PassportService::getInstance()->getLoginInfo();

        // 拼装语句
        $query = SysLogOptModel::find()->where(['=', 'company_id', $sysUserInfo['company_id']]);
        !is_null($where['sysUserId']) && $query->andWhere(['=', 'sys_user_id', $sysUserInfo['id']]);
        !is_null($where['optPage']) && $query->andWhere(['=', 'page', $where['optPage']]);
        !is_null($where['startTime']) && $query->andWhere(['between', 'created_time', $where['startTime'], $where['endTime']]);

        return $query;
    }

    /**
     * 获取操作人员列表
     *
     * @param $companyId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOperatorList($companyId)
    {
        return SysUserModel::find()
            ->select('id, user_name')
            ->where(['=', 'is_del', 0])
            ->andWhere(['=', 'company_id', $companyId])
            ->asArray()
            ->all();
    }

    /**
     * 获取操作页面列表
     *
     * @return mixed
     * @throws ServiceException
     */
    public function getOptPageList()
    {
        $data = SysGlobalVarModel::find()->where(['=', 'key', 'OPT_PAGE_LIST'])->asArray()->one();

        if (empty($data['val'])) {
            throw new ServiceException('不存在OPT_PAGE_LIST值');
        }

        return Json::decode($data['val'], true);
    }
}
