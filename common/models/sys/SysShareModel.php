<?php

namespace app\modules\common\models\sys;

use app\modules\common\enums\TimeEnum;
use app\modules\common\enums\YesOrNoEnum;
use app\modules\common\models\BaseModel;
use app\modules\sys\enums\ShareEnum;
use yii\base\Event;

class SysShareModel extends BaseModel
{

    public static function tableName()
    {
        return 'sys_share';
    }


    public function getPlanList(int $offset =0, int $limit = 20)
    {
        return self::find()
            ->where('type = :type')
            ->andWhere('status = :status')
            ->params(['type' => ShareEnum::PLAN, 'status' => YesOrNoEnum::YES])
            ->offset($offset)
            ->limit($limit)
            ->orderBy('id desc')
            ->all();
    }

    public function planTotal()
    {
        $total = self::find()
            ->where('type = :type')
            ->params(['type' => ShareEnum::PLAN])
            ->count();
        return (int)$total;
    }

    public function findUserShareByUserId(int $userId)
    {
        return self::find()
            ->where('user_id = :userId')
            ->andWhere('type = :type')
            ->params(['userId' => $userId, 'type' => ShareEnum::USER])
            ->cache(TimeEnum::ONE_HOUR)
            ->one();
    }

    public function findByUserIds(array $userIds)
    {
        return self::find()
            ->where(['in', 'user_id', $userIds])
            ->all();
    }

    public function findByToken(string $token)
    {
        return self::find()
            ->where('token = :token')
            ->params(['token' => $token])
            ->one();
    }

    public function likeByPromoter(string $promoter)
    {
        return self::find()
            ->where(['like', 'promoter', $promoter])
            ->all();
    }

    public function addCallback(Event $event)
    {
        $userId = (int)$event->sender->user_id;
        $type = (int)$event->sender->type;
        if ($type == ShareEnum::USER) {
            $this->findUserShareByUserId($userId);
            $this->deleteCache();
        }
    }
}
