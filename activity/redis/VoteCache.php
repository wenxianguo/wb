<?php

namespace app\modules\activity\redis;

use app\modules\common\enums\TimeEnum;
use app\modules\common\redis\BaseCache;

class VoteCache extends BaseCache
{
    const VOTE = 'wb:activity:vote:%d_%s';

    public function incrVote(int $activityId,string $openId)
    {
        $cacheKey = $this->getVoteKey($activityId, $openId);
        $flag = $this->redis->incr($cacheKey);
        $second = strtotime(date('Y-m-d')) + TimeEnum::ONE_DAY - time();
        $this->redis->expire($cacheKey, $second);
        return $flag;
    }

    public function getVote(int $activityId, string $openId)
    {
        $cacheKey = $this->getVoteKey($activityId, $openId);
        return $this->redis->get($cacheKey);
    }

    private function getVoteKey(int $activityId, string $openId)
    {
        return sprintf(self::VOTE, $activityId, $openId);
    }
}
