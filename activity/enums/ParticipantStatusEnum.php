<?php

namespace app\modules\activity\enums;

use app\modules\common\enums\BaseEnum;

class ParticipantStatusEnum extends BaseEnum
{
    //未审核
    const NOT_REVIEWED = 1;

    //已审核
    const REVIEWED = 2;
}
