<?php

namespace app\modules\sys\redis;

use app\modules\common\redis\BaseCache;

class PhoneCache extends BaseCache
{
    const PHONE_CODE_COUNT = 'phone_code_count_%d';

    const EXPIRE = 3600;

    public function setCountByPhone($phone)
    {
        $cacheKey = $this->getCodeCacheKeyByPhone($phone);
        $this->redis->set($cacheKey, 0);
        $this->redis->expire($cacheKey, self::EXPIRE);
    }

    public function incrCountByPhone(int $phone)
    {
        $cacheKey = $this->getCodeCacheKeyByPhone($phone);
        $this->redis->incr($cacheKey);
    }

    public function getCountByPhone(int $phone)
    {
        $cacheKey = $this->getCodeCacheKeyByPhone($phone);
        return $this->redis->get($cacheKey);
    }

    private function getCodeCacheKeyByPhone(int $phone)
    {
        return sprintf(self::PHONE_CODE_COUNT, $phone);
    }
}
