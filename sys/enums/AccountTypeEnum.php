<?php

namespace app\modules\sys\enums;

use app\modules\common\enums\BaseEnum;

class AccountTypeEnum extends BaseEnum
{
    // fb
    const FB = 1;

    // 字节跳动
    const TIKTOK = 2;

    // google
    const GOOGLE = 3;

    const MESSAGE = [
        self::FB => 'Facebook',
        self::TIKTOK => 'Tiktok',
        self::GOOGLE => 'Google'
    ];
}
