<?php

namespace app\modules\sys\enums;

use app\modules\common\enums\BaseEnum;

class CompanyIdEnum extends BaseEnum
{
    // 默认值，没有所属公司
    const DEFAULT = 1;

    // 汇量
    const MOBVISTA = 2;

    // 店匠
    const SHOPLAZZA = 3;

    //shopify
    const SHOPIFY = 4;

    const SHOPSCANNER = 5;

    const MESSAGE = [
        self::MOBVISTA => 'mobvista',
        self::SHOPIFY => 'shopify',
        self::SHOPLAZZA => 'shoplazza',
        self::SHOPSCANNER => 'shopscanner'
    ];
}
