<?php

namespace app\modules\common;

use app\modules\common\events\ConsultationCreatedEvent;
use app\modules\common\listeners\SendEmailOnConsultationCreatedEvent;

/**
 * 事件提供器
 * Class EventProvider
 * @package app\modules\fb
 */
class EventProvider
{
    /**
     * 绑定事件，key是事件，value是监听者，可以有多个监听者
     */
    const BINDS = [
        ConsultationCreatedEvent::class => [
            SendEmailOnConsultationCreatedEvent::class
        ],
    ];
}
