<?php
declare(strict_types=1);
namespace app\modules\common\enums;

/**
 * 权限
 * Class PermissionEnum
 * @package app\modules\common\enums
 */
class PermissionEnum extends BaseEnum
{
    /**
     * 平台账号
     */
    const PLATFORM_ACCOUNT = 'PLATFORM_ACCOUNT';

    /**
     * 创建广告
     */
    const CREATE_AD = 'CREATE_AD';

    /**
     * fb开户
     */
    const FB_CREATE_ACCOUNT = 'FB_CREATE_ACCOUNT';

    /**
     * 开户运营管理
     */
    const OPERATE_FB_CREATE_ACCOUNT = 'OPERATE_FB_CREATE_ACCOUNT';

    /**
     * 用户信息
     */
    const PROFILE = 'PROFILE';

    /**
     * 充值
     */
    const RECHARGE = 'RECHARGE';

    /**
     * 充值运营管理
     */
    const OPERATE_RECHARGE = 'OPERATE_RECHARGE';

    /**
     * 充值运营管理
     */
    const TIKTOK_CREATE_ACCOUNT = 'TIKTOK_CREATE_ACCOUNT';

    /**
     * 充值运营管理
     */
    const OPERATE_TIKTOK_CREATE_ACCOUNT = 'OPERATE_TIKTOK_CREATE_ACCOUNT';

    /**
     * 充值运营管理
     */
    const GOOGLE_CREATE_ACCOUNT = 'GOOGLE_CREATE_ACCOUNT';

    /**
     * 充值运营管理
     */
    const OPERATE_GOOGLE_CREATE_ACCOUNT = 'OPERATE_GOOGLE_CREATE_ACCOUNT';

    /**
     * 账号管理记录
     */
    const OPERATE_MANAGEMENT_ACCOUNT = 'OPERATE_MANAGEMENT_ACCOUNT';
}
