<?php

namespace app\modules\common\models\sys;

use app\modules\common\enums\TimeEnum;
use app\modules\common\models\BaseModel;
use yii\base\Event;

class SysGlobalVarModel extends BaseModel
{
    // json类型的对象
    const TYPE_JSON_OBJECT = 1;
    // 字符
    const TYPE_STRING = 2;

    //浮点型
    const TYPE_FLOAT = 3;

    //整形
    const TYPE_INT = 4;

    public static function tableName()
    {
        return 'sys_global_var';
    }

    public function findByKey(string $key)
    {
        return self::find()
            ->where('`key` = :key')
            ->params(['key' => $key])
            ->cache(TimeEnum::ONE_HOUR)
            ->limit(1)
            ->one();
    }

    public function findByKeys(array $keys)
    {
        return self::find()
            ->where(['in', 'key', $keys])
            ->all();
    }

    public function updateCallback(Event $event)
    {
        $key = $event->sender->key;
        $this->findByKey($key);
        $this->deleteCache();
    }
}
