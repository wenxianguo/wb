<?php

namespace app\modules\common\models\activity;

use app\modules\common\models\BaseModel;

class VoteModel extends BaseModel
{
    public static function tableName()
    {
        return 'vote';
    }

    public function totalByActivityIdIdAndOpenId(int $activityId, string $openId)
    {
        $total = self::find()
            ->where(['=', 'activity_id', $activityId])
            ->andWhere(['=', 'open_id', $openId])
            ->count();
        return (int) $total;
    }
}
