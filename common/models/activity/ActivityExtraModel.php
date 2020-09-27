<?php

namespace app\modules\common\models\activity;

use app\modules\common\models\BaseModel;

class ActivityExtraModel extends BaseModel
{
    public static function tableName()
    {
        return 'activity_extra';
    }

    public function findByActivityId(int $activityId)
    {
        return self::find()
            ->where(['=', 'activity_id', $activityId])
            ->one();
    }
}
