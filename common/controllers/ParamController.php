<?php

namespace app\modules\common\controllers;

use app\modules\common\assemble\common\ParamAssemble;

/**
 * 返回功能参数
 * Class ParamController
 * @package app\modules\common\controllers
 */
class ParamController extends BaseController
{

    /**
     * 订单详情
     *
     * @http GET
     *
     *
     * @params
     *  - field | string | 字段 | {current_time,buy_amount_minimum,payment_type,placeholder,call_to_action,label_language,label_type,label_period,label_position,campaign_name_rule,adset_name_rule,ad_name_rule,tiktok_product_type,tiktok_popularize_address,tiktok_contact_email,notice,google_agent,mc_sizes} | Y
     *
     * @response
     * {"data":{"current_time":1573786694,"buy_amount_minimum":10,"payment_type":{"chinese":[{"name":"微信","type":1},{"name":"支付宝","type":2},{"name":"银联","type":3}],"english":[{"name":"wechat","type":1},{"name":"alipay","type":2},{"name":"unionpay","type":3}]}},"msg":"Success","code":0}
     * @fields
     * - current_time | 服务器的当前时间
     * - buy_amount_minimum | 美元金额最小值
     * - payment_type | 支付类型
     * - placeholder | 占位符
     * - call_to_action | 行动在号召
     * - campaign_name_rule | campaign命名规则
     * - adset_name_rule | adset命名规则
     * - ad_name_rule | ad命名规则
     * - tiktok_product_type | 产品类型
     * - tiktok_popularize_address | 推荐地址
     * - tiktok_contact_email | 联系邮箱
     * - notice | 公告
     * - google_agent | google 开户代理
     * - fb_remark | fb 备注列表
     * - tiktok_remark | tiktok 备注列表
     * - google_remark | google 备注列表
     * - mc_sizes | 素材尺寸
     */
    public function actionDetail()
    {
        $param = ['current_time' => time()];
        $data = $this->assemble(ParamAssemble::class, $param);

        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }
}
