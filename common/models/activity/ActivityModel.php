<?php

namespace app\modules\common\models\activity;

use app\modules\common\models\BaseModel;

class ActivityModel extends BaseModel
{
    public static function tableName()
    {
        return 'activity';
    }

    public function findByUserId(int $userId, int $offset = 0, int $limit = 20)
    {
        return self::find()
            ->where(['=', 'user_id', $userId])
            ->orderBy('created_time desc')
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
    }

    public function totalByUserId(int $userId)
    {
        $total = self::find()
            ->where(['=', 'user_id', $userId])
            ->count();
        return (int)$total;
    }
}
