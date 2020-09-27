<?php

namespace app\modules\sys\redis;

use app\modules\common\redis\BaseCache;

class UserCache extends BaseCache
{
    /**
     * 访问系统的用户集，每天统计一次，用bitmap的数据结构来存储
     */
    const ACCESS_SYSTEM_USER = 'slr:sys:access_user:bit:%d';

    const EXPIRE = 7 * 86400;

    public function setBitByUserId(int $userId)
    {
        $cacheKey = $this->getAccessSystemUserKey();
        $this->redis->setbit($cacheKey, $userId, 1);
        $this->redis->expire($cacheKey, self::EXPIRE);
    }

    public function bitCountByUserId()
    {
        $cacheKey = $this->getAccessSystemUserKey();
        $count = $this->redis->bitcount($cacheKey);
        return $count;
    }

    private function getAccessSystemUserKey()
    {
        $date = date('Ymd', time());
        return sprintf(self::ACCESS_SYSTEM_USER, $date);
    }
}
