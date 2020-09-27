<?php

namespace app\modules\common\models\sys;

use app\modules\common\models\BaseModel;

class SysVerifyCodeModel extends BaseModel
{
    //注册发送短信验证码
    const TYPE_REGISTER_PHONE = 1;
    //注册发送邮件验证码
    const TYPE_REGISTER_EMAIL = 2;
    //绑定手机验证码
    const TYPE_BIND_PHONE = 3;
    //绑定邮箱验证码
    const TYPE_BIND_EMAIL = 4;
    //忘记密码发送短信验证码
    const TYPE_RESET_PASSWORD_PHONE = 5;
    //忘记密码发送重置密码的邮箱验证码
    const TYPE_RESET_PASSWORD_EMAIL_PAGE = 6;
    //忘记密码验证短信验证码后的页面token
    const TYPE_RESET_PASSWORD_PHONE_PAGE = 7;
    //管理员新增user到企业小组的页面token
    const TYPE_ADD_GROUP_USER_PAGE = 8;


    public static function tableName()
    {
        return 'sys_verify_code';
    }

}
