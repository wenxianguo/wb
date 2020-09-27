<?php

namespace app\modules\common\models\sys;

use app\modules\common\enums\TimeEnum;
use app\modules\common\models\BaseModel;
use yii\base\Event;

class SysUserExtraModel extends BaseModel
{

    public static function tableName()
    {
        return 'sys_user_extra';
    }

    public function findByUserId(int $userId)
    {
        return self::find()
            ->where('user_id = :userId')
            ->params(['userId' => $userId])
            ->cache(TimeEnum::HALF_HOUR)
            ->one();
    }

    public function likeBySssUserName(string $sssUserName)
    {
        return self::find()
            ->where(['like', 'sss_user_name', $sssUserName])
            ->all();
    }

    public function findShareIds(array $shareIds)
    {
        return self::find()
            ->where(['in', 'share_id', $shareIds])
            ->all();
    }

    public function addCallback(Event $event)
    {
        $this->flushCache($event);
    }

    public function updateCallback(Event $event)
    {
        $this->flushCache($event);
    }

    private function flushCache(Event $event)
    {
        $userId = (int)$event->sender->user_id;
        $this->findByUserId($userId);
        $this->deleteCache();
    }
}
