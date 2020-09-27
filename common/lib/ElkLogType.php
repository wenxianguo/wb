<?php

namespace app\modules\common\lib;
class ElkLogType
{
    // 请求task_api获取token
    const FB_SDK_API_GET_TOKEN = 'FB_SDK_API_GET_TOKEN';

    // 请求task_api获取token
    const FB_CREATE_USER = 'FB_CREATE_USER';

    // 请求task_api获取user
    const FB_SDK_API_GET_USER = 'FB_SDK_API_GET_USER';

    // 请求task_api推送account
    const FB_SDK_API_PUSH_ACCOUNT = 'FB_SDK_API_PUSH_ACCOUNT';

    // 请求获取授权地址
    const SC_SDK_API_GET_AUTH_URL = 'SC_SDK_API_GET_AUTH_URL';

    // 请求刷新access_token
    const SC_SDK_API_REFRESH_ACCESS_TOKEN = 'SC_SDK_API_REFRESH_ACCESS_TOKEN';

    // 系统错误
    const SYSTEM_ERROR = 'SYSTEM_ERROR';

    // 系统错误
    const SYSTEM_INFO = 'SYSTEM_INFO';

    // 网络请求错误
    const NETWORK_REQUEST_ERROR = 'NETWORK_REQUEST_ERROR';

    // 操作日志记录失败
    const OPT_LOG_ERROR = 'OPT_LOG_ERROR';

    // 开户的创建请求
    const FACEBOOK_OPEN_ACCOUNT_CREATE = 'facebook_open_account_create';

    // 开户的更新请求
    const FACEBOOK_OPEN_ACCOUNT_UPDATE = 'facebook_open_account_update';

    // 发送邮件失败
    const SEND_EMAIL_FAILED = 'send_email_failed';

    // 问题咨询
    const CONSULTATION = 'consultation';

    //充值创建请求
    const RECHARGE_CREATE = 'RECHARGE_CREATE';

    //微信支付
    const WECHAT_PAYMENT = 'WECHAT_PAYMENT';

     //充值更新请求
    const RECHARGE_UPDATE = 'recharge_update';

    //支付回调
    const RECHARGE_PAYMENT_CALLBACK = 'recharge_payment_callback';

    //支付回调
    const RECHARGE_PAYMENT_FAILED = 'recharge_payment_failed';

    //支付回调页面
    const RECHARGE_PAYMENT_CALLBACK_BG = 'recharge_payment_callback_bg';

    //shopify
    const WEB_HOOK_CUSTOMER_CREATE = 'WEB_HOOK_CUSTOMER_CREATE';

    const WEB_HOOK_CUSTOMER_REDACT = 'WEB_HOOK_CUSTOMER_REDACT';

    const WEB_HOOK_SHOP_REDACT = 'WEB_HOOK_SHOP_REDACT';

    //处理product的事件
    const WEB_HOOK_PRODUCT = 'WEB_HOOK_PRODUCT';

    //处理 mc product的事件
    const WEB_HOOK_MC_PRODUCT = 'WEB_HOOK_MC_PRODUCT';

    //处理 卸载 shop 的事件
    const WEB_HOOK_UNINSTALL_SHOP = 'WEB_HOOK_UNINSTALL_SHOP';

    //处理 卸载 shop 的事件
    const WEB_HOOK_MC_UNINSTALL_SHOP = 'WEB_HOOK_MC_UNINSTALL_SHOP';

    //获取实时汇率
    const AIRWALLEX_MARKETFX = 'airwallex_marketfx';

    // 获取授权信息
    const AIRWALLEX_LOGIN = 'airwallex_login';

    //转汇接口
    const AIRWALLEX_CONVERSION_CREATE = 'airwallex_conversion_create';

    const AIRWALLEX_CONVERSION_DETAIL = 'airwallex_conversion_detail';

    const AIRWALLEX_BALANCE = 'airwallex_balance';

    //汇付支付
    const CHINAPNR_PAYMENT = 'chinapnr_payment';

    const REFUNDRECEIVE = 'REFUNDRECEIVE';

    const REFUNDRECEIVE_CALLBACK = 'REFUNDRECEIVE_CALLBACK';

    //店匠授权
    const SHOPLAZZA_AUTH = 'shoplazza_auth';

    //关联关系
    const SHOPLAZZA_RELATE = 'shoplazza_relate';

    //shopify 访问
    const SHOPIFY_VISIT = 'SHOPIFY_VISIT';

    //素材工具shopify 访问
    const MC_SHOPIFY_VISIT = 'MC_SHOPIFY_VISIT';

    // shopify的mc插件 套餐支付失败
    const MC_SHOPIFY_CHARGE_ERROR = 'MC_SHOPIFY_CHARGE_ERROR';

    //shopify 上传商品图片
    const SHOPIFY_UPDATE_PRODUCT_IMAGE = 'SHOPIFY_UPDATE_PRODUCT_IMAGE';

    // 自动建单
    const AUTOMATION_DELIVERY = 'automation_delivery';

    //shopify建单
    const AUTOMATION_DELIVERY_SHOPIFY = 'automation_delivery_shopify';

    //可视化 建单
    const AUTOMATION_DELIVERY_VISUALIZATION = 'automation_delivery_visualization';

    //用户登录情况
    const USER_LOGIN_INFO = 'user_login_info';

    // 登录错误类型
    const USER_LOGIN_ERROR_INFO = 'user_login_error_info';

    // 创建shopify链接错误
    const CREATE_SHORT_LINK_ERROR = 'create_short_link_error';

    //创建tiktok
    const TIKTOK_OPEN_ACCOUNT_CREATE = 'tiktok_open_account_create';

    //更新tiktok
    const TIKTOK_OPEN_ACCOUNT_UPDATE = 'tiktok_open_account_update';

    //创建 google
    const GOOGLE_OPEN_ACCOUNT_CREATE = 'google_open_account_create';

    //更新 google
    const GOOGLE_OPEN_ACCOUNT_UPDATE = 'google_open_account_update';

    const SQL_ERROR = 'sql_error';

    const ASSEMBLE_ERROR = 'ASSEMBLE_ERROR';

    const BATCH_UPDATE_ERROR = 'BATCH_UPDATE_ERROR';

    const AD_SYNC = 'AD_SYNC';
}
