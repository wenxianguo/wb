<?php

namespace app\modules\activity\controllers;

use app\modules\common\controllers\AuthBaseController;

class TestController extends AuthBaseController
{

     /**
     * 页面配置
     *
     * @alias page_config
     * 
     * @fields
     * - result | 投票结果，0、关闭；1、开启
     * - display_mode  | 显示方式；1、单排显示；2、双排显示
     * - page_display | 页面显示；1、瀑布流；2、对称显示
     * - home_page_number | 首页显示选手数量
     * - top_rotation | 顶部轮播，0、关闭；1、开启
     * - rotation_content | <b style="color:red">顶部轮播内容</b>
     */
    public function actionPageConfig()
    {
    }

    /**
     * 选手报名设置
     *
     * @alias player_setup
     * 
     * @fields
     * - name | 名称
     * - number_limit | 图片上传数量
     * - review | 选手报名审核，0、关闭；1、开启
     * - phone  | 手机号码，0、不显示；1、选填、2、必填
     * - address | 用户地址；，0、不显示；1、选填、2、必填
     * - description | 用户描述；，0、不显示；1、选填、2、必填
     * - extra | 扩展信息；数组格式，数组里面是对象格式
     */
    public function actionPlayerSetup()
    {
    }

    /**
     * 防刷票配置
     *
     * @alias prevent_swipe_tickets
     * 
     * @fields
     * - restricted_area | 限定地区投票，0、关闭；1、开启
     * - verification_code | 开启验证码，0、关闭；1、开启
     * - black_list | 开启黑名单，0、关闭；1、开启
     * - pop_up_verification  | 自动弹出验证码，0、关闭；1、开启
     * - everyday_number_limit | 选手每日获取票数上限(误差在10以内)；
     * - everyhour_number_limit | 选手每小时获取票数上限
     */
    public function actionPreventSwipeTickets()
    {
    }

    /**
     * 现场配置
     *
     *  @alias Live_voting
     * 
     * @fields
     * - live_voting | 现场投票，0、关闭；1、开启
     * - outside_share | 场外分享，0、关闭；1、开启
     */
    public function actionLiveVoting()
    {
    }
    /**
     * 其他配置
     *
     * @alias other_config
     * 
     * @fields
     * - bottom_text | 底部文字
     * - bottom_url | 底部链接
     * - vote_record | 用户投票记录，0、关闭；1、开启
     */
    public function actionOtherConfig()
    {
    }
}
