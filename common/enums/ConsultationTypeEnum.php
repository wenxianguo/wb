<?php

namespace app\modules\common\enums;

class ConsultationTypeEnum extends BaseEnum
{
    const OPEN_ACCOUNT_PAGE = 1;

    const RECHARGE_RECORD = 2;

    const RECHARGE = 3;

    const OPEN_ACCOUNT_NOT_COMPLETE = 4;

    const OTHER = 5;

    const MESSAGE = [
        self::OPEN_ACCOUNT_PAGE => '开户管理',
        self::RECHARGE_RECORD => '充值记录',
        self::RECHARGE => '充值',
        self::OPEN_ACCOUNT_NOT_COMPLETE => '开户未完成',
        self::OTHER => '其他'
    ];
}
