<?php

namespace app\modules\common\enums;

/**
 * 日期常量
 * Class TimeEnum
 * @package app\modules\common\enums
 */
class TimeEnum extends BaseEnum
{
    /**
     * 一分钟
     */
    const ONE_MINUTE = 60;

    /**
     * 五分钟
     */
    const FIVE_MINUTE = 300;

    /**
     * 十分钟
     */
    const TEN_MINUTES = 600;

    /**
     * 15分钟
     */
    const FIFTEEN_MINUTE = 900;

    /**
     * 半小时
     */
    const HALF_HOUR = 1800;

    /**
     * 一个小时
     */
    const ONE_HOUR = 3600;

    /**
     * 一天
     */
    const ONE_DAY = 86400;
}
