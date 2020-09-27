<?php

namespace app\modules\common\redis;

use yii\redis\Connection;

class BaseCache
{
    /** @var Connection $redis */
    public $redis;
    public function __construct(BaseRedis $baseRedis)
    {
        $this->redis = $baseRedis;
    }
}
