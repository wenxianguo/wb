<?php
declare(strict_types=1);
namespace app\modules\common\models\sys;

use app\modules\common\enums\TimeEnum;
use app\modules\common\models\BaseModel;
use yii\base\Event;

class SysAccountModel extends BaseModel
{
    public static function tableName()
    {
        return 'sys_account';
    }

    public function findByUserIdAndType(int $userId, int $type)
    {
        return self::find()
            ->where('user_id = :userId')
            ->andWhere('type = :type')
            ->params(['userId' => $userId, 'type' => $type])
            ->asArray()
            ->all();
    }

    public function findByAccountIdAndUserIdAndType($accountId, int $userId, int $type)
    {
        return self::find()
            ->where('user_id = :userId')
            ->andWhere('account_id = :accountId')
            ->andWhere('type = :type')
            ->params(['userId' => $userId, 'accountId' => $accountId, 'type' => $type])
            ->cache(TimeEnum::ONE_HOUR)
            ->one();
    }

    public function likeByAccountNameAndType(string $accountName, int $type)
    {
        return self::find()
            ->where(['like', 'account_name', $accountName])
            ->andWhere(['=', 'type', $type])
            ->asArray()
            ->all();
    }

    public function updateCallback(Event $event)
    {
        $accountId = (int)$event->sender->account_id;
        $userId = (int)$event->sender->user_id;
        $type = (int)$event->sender->type;
        $this->findByAccountIdAndUserIdAndType($accountId, $userId, $type);
        $this->deleteCache();
    }
}
