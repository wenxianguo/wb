<?php

namespace app\modules\common\models\activity;

use app\modules\common\models\BaseModel;

class ParticipantModel extends BaseModel
{
    public $softDelete = false;
    public static function tableName()
    {
        return 'participant';
    }

    public function findByUserIdActivityId(int $userId, int $activityId)
    {
        return self::find()
            ->where(['=', 'user_id', $userId])
            ->andWhere(['=', 'activity_id', $activityId])
            ->asArray()
            ->all();
    }

    public function findByActivityId(int $activityId)
    {
        return self::find()
            ->where(['=', 'activity_id', $activityId])
            ->asArray()
            ->all();
    }

    public function findBySearch(array $where, string $order, int $offset = 0, int $limit = 20)
    {
        return self::find()
            ->where($where)
            ->orderBy($order)
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
    }

    public function getMaxNumberByActivityId(int $activityId)
    {
        return self::find()
        ->select('number')
        ->where(['=', 'activity_id', $activityId])
        ->orderBy('number desc')
        ->limit(1)
        ->asArray()
        ->one();
    }

    public function totalBySearch(array $where)
    {
        $total = self::find()
            ->where($where)
            ->count();
        return $total;
    }
}
