<?php

namespace app\modules\common\enums;

class DeleteTypeEnum extends BaseEnum
{
    /**
     * 单个
     */
    const SINGLE = 'fetch';

    /**
     * 列表
     */
    const LIST = 'fetchAll';
}
