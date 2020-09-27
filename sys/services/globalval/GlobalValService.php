<?php
namespace app\modules\sys\services\globalval;

use yii\helpers\Json;
use app\modules\common\models\sys\SysGlobalVarModel;


class GlobalValService
{
    private $sysGlobalVarModel;
    public function __construct(SysGlobalVarModel $sysGlobalVarModel)
    {
        $this->sysGlobalVarModel = $sysGlobalVarModel;
    }

    /**
     * 获取配置
     * @param string $key
     * @return array
     */
    public function getByKey(string $key)
    {
        $item = $this->sysGlobalVarModel->findByKey($key);
        if (!$item) {
            return null;
        }
        $res = $this->getVal($item->type, $item->val);
        return $res;
    }

    public function getByKeys(array $keys)
    {
        $items = $this->sysGlobalVarModel->findByKeys($keys);
        $res = [];
        foreach ($items as $item) {
            $res[$item->key] = $this->getVal($item->type, $item->val);
        }
        return $res;
    }

    public function setVal(string $key, string $val)
    {
        $globalVar = $this->sysGlobalVarModel->findByKey($key);
        if ($globalVar) {
            $data['val'] = $val;
            $this->sysGlobalVarModel->updateModel($globalVar, $data);
        }
    }

    private function getVal(int $type, string $value)
    {
        switch ($type) {
            case SysGlobalVarModel::TYPE_JSON_OBJECT:
                $res = !empty($value) ? Json::decode($value, true) : [];
                break;
            case SysGlobalVarModel::TYPE_STRING:
                $res = trim($value);
                break;
            case SysGlobalVarModel::TYPE_FLOAT:
                $res = (float)$value;
                break;
            case SysGlobalVarModel::TYPE_INT:
                $res = (int)$value;
                break;
            default:
                $res = '';
                break;
        }
        return $res;
    }

}