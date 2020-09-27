<?php

namespace app\modules\common\models\activity;

use app\modules\common\models\BaseModel;

class ImageModel extends BaseModel
{
    public $softDelete = false;
    public static function tableName()
    {
        return 'image';
    }

    public function findByObjectIdAndType(int $objectId, int $type)
    {
        return self::find()
            ->where(['=', 'object_id', $objectId])
            ->andWhere(['=', 'type', $type])
            ->asArray()
            ->all();
    }
}
