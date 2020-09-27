<?php

namespace app\modules\common\enums;

class OssPathEnum extends BaseEnum
{
    /**
     * 表单咨询
     */
    const CONSULTATION = 1;

    /**
     * 签约主体
     */
    const CONTRACTING_ENTITY = 2;

    /**
     * 可视化
     */
    const VISUALIZATION = 3;

    /**
     * tiktok 开户的营业执照
     */
    const TIKTOK_BUSINESS_LICENSE = 4;

    /**
     * tiktok 的备注图片
     */
    const TIKTOK_REMARK_IMAGE = 5;

    /**
     * google 开户的营业执照
     */
    const GOOGLE_BUSINESS_LICENSE = 6;

    /**
     * google 的备注图片
     */
    const GOOGLE_REMARK_IMAGE = 7;

    /**
     * fb 的备注图片
     */
    const FB_REMARK_IMAGE = 8;

    /**
     * shopify 类型
     */
    const SHPIFY = 10;

    const MESSAGE = [
        self::CONSULTATION => 'consultation',
        self::CONTRACTING_ENTITY => 'contracting_entity',
        self::VISUALIZATION => 'visualization',
        self::TIKTOK_BUSINESS_LICENSE => 'tiktok',
        self::TIKTOK_REMARK_IMAGE => 'tiktok',
        self::GOOGLE_BUSINESS_LICENSE => 'google',
        self::GOOGLE_REMARK_IMAGE => 'google',
        self::FB_REMARK_IMAGE => 'fb',
    ];

    const IS_NOT_OSS = [
    ];
}
