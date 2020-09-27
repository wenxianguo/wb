<?php

namespace app\modules\common\events;

use yii\base\Event;

class BaseEvent extends Event
{
    public function getNamespace()
    {
        return static::class;
    }
}
