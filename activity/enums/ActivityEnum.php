<?php

namespace app\modules\activity\enums;

use app\modules\common\enums\BaseEnum;

class ActivityEnum extends BaseEnum
{
    //参与时间倒叙
    const SORT_CREATE_TIME_DESC = 1;

    //投票数从高到低倒叙
    const SORT_VOTE_COUNT_DESC = 2;

    //编号从低到高倒叙
    const SORT_NUMBER_ASC = 3;

    //关闭
    const CLOSE = 0;

    //开启
    const OPEN = 1;

    //显示方式  ---单排显示
    const DISPLAY_SINGLE_ROW = 1;

    //显示方式 -- 双排显示
    const DISPLAY_DOUBLE_ROW = 2;

    //页面显示 -- 瀑布流
    const PAGE_WATERFALL_FLOW = 1;

    //页面展示 -- 对称显示
    const PAGE_SYMMETRIC = 2;

    // 选填
    const VERIFICATION_OPTIONAL = 1;

    //必填
    const VERIFICATION_REQUIRED = 2;

    //隐藏
    const VERIFICATION_HIDE = 3;

    const MESSAGE = [
        self::SORT_CREATE_TIME_DESC => 'created_time desc',
        self::SORT_VOTE_COUNT_DESC => 'vote_count desc',
        self::SORT_NUMBER_ASC => 'number asc',
    ];
}
