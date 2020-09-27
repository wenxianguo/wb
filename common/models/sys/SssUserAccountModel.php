<?php

namespace app\modules\common\models\sys;

use app\modules\common\enums\TimeEnum;
use app\modules\common\models\BaseModel;

class SssUserAccountModel extends BaseModel
{

    public static function tableName()
    {
        return 'fmp_adv_list';
    }

    public static function getDb()
    {
        return \Yii::$app->sss_db;
    }

    public function findByUsername(string $username)
    {
        return self::find()
            ->where('sss_adv_username = :username')
            ->params(['username' => $username])
            ->one();
    }

    public function findByTeamAndPlatform(string $team, string $platform, string $field = 'account_id')
    {
        return self::find()
            ->select($field)
            ->where('biz_team = :team')
            ->andWhere('platform_key = :platform')
            ->params(['team' => $team, 'platform' => $platform])
            ->cache(TimeEnum::TEN_MINUTES)
            ->asArray()
            ->all();
    }
}
