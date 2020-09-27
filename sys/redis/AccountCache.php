<?php

namespace app\modules\sys\redis;

use app\modules\common\config\QueueConfig;
use app\modules\common\redis\BaseCache;

class AccountCache extends BaseCache
{
    const SYS_ACCOUNT_USER_ID = 'slr:sys:account:user_id_type_%d_%d:string';

    const EXPIRE = 3600;

    public function setAccountsByUserId(int $userId, int $type, array $data)
    {
        $cacheKey = $this->getAccountsKey($userId, $type);
        $data = json_encode($data);
        $this->redis->set($cacheKey, $data);
        $this->redis->expire($cacheKey, self::EXPIRE);
    }

     public function getAccountsByUserId(int $userId, int $type)
    {
        $cacheKey = $this->getAccountsKey($userId, $type);
        $data = $this->redis->get($cacheKey);
        $data = json_decode($data, true);
        return $data;
    }

     public function deleteAccountsByUserId(int $userId, int $type)
    {
        $cacheKey = $this->getAccountsKey($userId, $type);
        return $this->redis->del($cacheKey);
    }

    public function syncAccount(int $userId, string $sssUsername)
    {
        $data = [
            'user_id' => $userId,
            'sss_user_name' => $sssUsername
        ];
        $data = \GuzzleHttp\json_encode($data);
        $this->redis->lpush(QueueConfig::SYNC_3S_ACCOUNT, $data);
    }

    private function getAccountsKey(int $userId, int $type)
    {
        return sprintf(self::SYS_ACCOUNT_USER_ID, $userId, $type);
    }
}
