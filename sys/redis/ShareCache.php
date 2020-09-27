<?php

namespace app\modules\sys\redis;

use app\modules\common\redis\BaseCache;

class ShareCache extends BaseCache
{
    /**
     * 访问系统的用户集，每天统计一次，用bitmap的数据结构来存储
     */
    const SHARE_PLAN = 'slr:sys:share_plan:set';
    const SHARE_CHANNEL = 'slr:sys:share_channel:set';
    const SHARE_PROMOTER = 'slr:sys:share_promoter:set';

    const EXPIRE = 7 * 86400;

    public function zAddPlan(string $plan)
    {
        $this->redis->zadd(self::SHARE_PLAN, time(), $plan);
    }

    public function zrevrangePlan(int $start, int $end)
    {
        return $this->redis->zrevrange(self::SHARE_PLAN, $start, $end);
    }

    public function zAddChannel(string $channel)
    {
        $this->redis->zadd(self::SHARE_CHANNEL, time(), $channel);
    }

    public function zrevrangeChannel(int $start, int $end)
    {
        return $this->redis->zrevrange(self::SHARE_CHANNEL, $start, $end);
    }

    public function zAddPromoter(string $promoter)
    {
        $this->redis->zadd(self::SHARE_PROMOTER, time(), $promoter);
    }

    public function zrevrangePromoter(int $start, int $end)
    {
        return $this->redis->zrevrange(self::SHARE_PROMOTER, $start, $end);
    }
}
