<?php

namespace app\modules\activity\enums;

use app\modules\common\enums\BaseEnum;

class VoteEnum extends BaseEnum
{
    //周期性
    const TYPE_CYCLE = 'cycle';

    //固定性
    const TYPE_FLXED = 'fixed';
}
