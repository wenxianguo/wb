<?php

namespace app\modules\common\services;

use app\modules\common\models\sys\SysLogOptModel;
use app\modules\common\lib\ActOptMap;
use app\exceptions\ServiceException;
use app\modules\sys\services\user\PassportService;
use app\response\ErrorCode;
use Yii;
use yii\helpers\Json;
use Mobvista\MixTools\Src\Ip\Ip;

class OptLogService extends BaseService
{
    private $urlInfo;
    private $actOptMap;
    private $actConfig;

    private $cond;
    private $model;
    private $action;

    private $updateOldValue;
    private $updateNewValue;
    private $createNewValue;
    private $deleteOldValue;
    private $exportValue;

    /**
     * 构造函数
     *
     * @throws ServiceException
     */
    protected function init()
    {
        if (!Yii::$app->controller) {
            return false;
        }

        // 获取当前访问的路径
        $this->urlInfo = [
            'module' => strtolower(Yii::$app->controller->module->id),
            'controller' => strtolower(Yii::$app->controller->id),
            'action' => strtolower(Yii::$app->controller->action->id),
        ];

        // 获取act到opt的映射
        $this->actOptMap = ActOptMap::getMap();

        // 获取act的映射配置
        $this->actConfig = $this->getActOptMapConfig($this->urlInfo, $this->actOptMap);
    }

    /**
     * 判断是否存在act到opt的映射
     *
     * @param $urlInfo
     * @param $actOptMap
     * @return mixed
     * @throws ServiceException
     */
    private function getActOptMapConfig($urlInfo, $actOptMap)
    {
        if (!isset($actOptMap[$urlInfo['module']])) {
            throw new ServiceException('找不到对应的module', ErrorCode::OPT_NO_ACT);
        }

        if (!isset($actOptMap[$urlInfo['module']][$urlInfo['controller']])) {
            throw new ServiceException('找不到对应的controller', ErrorCode::OPT_NO_ACT);
        }

        if (!isset($actOptMap[$urlInfo['module']][$urlInfo['controller']][$urlInfo['action']])) {
            throw new ServiceException('找不到对应的action', ErrorCode::OPT_NO_ACT);
        }

        return $actOptMap[$urlInfo['module']][$urlInfo['controller']][$urlInfo['action']];
    }

    /**
     * 设置查询条件
     *
     * @param $model
     * @param $action
     * @param array $cond
     * @return $this
     */
    public function setCond($model, $action, $cond = [])
    {
        $this->cond = $cond;
        $this->model = $model;
        $this->action = $action;

        return $this;
    }

    /**
     * 埋点，设置updated的旧值
     *
     * @param null $data
     * @return $this
     */
    public function setUpdateOldValue($data = null)
    {
        $this->updateOldValue = !empty($data) ? $data : ($this->model)::find()->where($this->cond)->asArray()->all();
        return $this;
    }

    /**
     * 埋点，设置updated的新值
     *
     * @param null $data
     * @return $this
     */
    public function setUpdateNewValue($data = null)
    {
        $this->updateNewValue = !empty($data) ? $data : ($this->model)::find()->where($this->cond)->asArray()->all();
        return $this;
    }

    /**
     * 写入删除的数据
     *
     * @param null $data
     * @return $this
     */
    public function setDeleteOldValue($data = null)
    {
        $this->deleteOldValue = !empty($data) ? $data : ($this->model)::find()->where($this->cond)->asArray()->all();
        return $this;
    }

    /**
     * 写入创建的数据
     *
     * @param $data
     * @return $this
     */
    public function setCreateNewValue($data)
    {
        $this->createNewValue = $data;
        return $this;
    }

    /**
     * 导出的数据
     * @param $data
     * @return $this
     */
    public function setExportValue($data)
    {
        $this->exportValue = $data;
        return $this;
    }

    /**
     * 记录日志
     *
     * @return int|string
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function log()
    {
        if ($this->action == 'updated') {
            $content = [
                'cond' => $this->cond,
                'old_value' => $this->updateOldValue,
                'new_value' => $this->updateNewValue
            ];
        } elseif ($this->action == 'created') {
            $content = [
                'new_value' => $this->createNewValue
            ];
        } elseif ($this->action == 'deleted') {
            $content = [
                'cond' => $this->cond,
                'old_value' => $this->deleteOldValue
            ];
        } elseif ($this->action == 'exported'){
            $content = [
                'cond' => $this->cond,
                'value' => $this->exportValue,
            ];
        } else {
            throw new ServiceException('action只支持updated | created | deleted | exported动作');
        }

        if($this->urlInfo){
            //TODO 暂时没有办法，底层调用了上层的代码
            $sysUserInfo = PassportService::getInstance()->getLoginInfo();

            $data = [
                'page' => $this->actOptMap[$this->urlInfo['module']][$this->urlInfo['controller']]['comment'],
                'operation' => $this->actOptMap[$this->urlInfo['module']][$this->urlInfo['controller']][$this->urlInfo['action']]['comment'],
                'content' => Json::encode($content, JSON_UNESCAPED_UNICODE),
                'sys_user_id' => $sysUserInfo['id'],
                'company_id' => $sysUserInfo['company_id'],
                'created_time' => time(),
                'table_name' => ($this->model)::tableName(),
                'ip' => Ip::getClientIp(),
                'type' => $this->action
            ];

            return SysLogOptModel::insertData($data);
        }
    }
}