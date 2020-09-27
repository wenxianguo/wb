<?php

namespace app\modules\common\models\sys;

use app\modules\common\models\BaseModel;

class SssUserModel extends BaseModel
{

    public static function tableName()
    {
        return 'sss_user_bd_pm_am';
    }

    public static function getDb()
    {
        return \Yii::$app->sss_db;
    }

    public function getList(int $offset =0, int $limit = 20)
    {
        return self::find()
            ->orderBy('adv_id desc')
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
    }

    public function likeByUsername(string $username, int $offset =0, int $limit = 20)
    {
        return self::find()
            ->where(['like', 'username', $username])
            ->orderBy('adv_id desc')
            ->offset($offset)
            ->limit($limit)
            ->asArray()
            ->all();
    }
}
