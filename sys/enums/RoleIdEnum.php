<?php

namespace app\modules\sys\enums;

use app\modules\common\enums\BaseEnum;

class RoleIdEnum extends BaseEnum
{
    // 默认值，普通权限
    const DEFAULT = 1;

    // 汇量超级管理员
    const MOBVISTA_ADMIN = 2;

    // 汇量的运营
    const MOBVISTA_OPERATE = 3;

    // 汇量的优化师
    const MOBVISTA_OPTIMIZER = 4;
}
