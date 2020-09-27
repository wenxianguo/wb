<?php

namespace app\modules\activity\controllers;

use app\modules\activity\services\ActivityService;
use app\modules\common\assemble\activity\ActivityAssemble;
use app\modules\common\controllers\BaseController;

class FrontActivityController extends BaseController
{
    private $fields = ['title', 'start_time', 'end_time', 'cover', 'qrcode', 'view_count', 'vote_count', 'user_count', ];
    private $voteConfigFields = ['vote_mode', 'vote_limit', 'restricted_voting'];
    private $extraFields = ['description', 'skin', 'color', 'sort', 'sign_up', 'position', 
    'floating_objects', 'background_skin', 'border_style', 'music'];
    private $extraArrFields = ['page_config'];

    private $activityService;
    public function __construct($id, $module, 
    ActivityService $activityService,  $config = [])
    {
        $this->activityService = $activityService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 获取活动详情
     *
     * @http GET
     * @alias activity/front-activities/1
     * 
     * @params
     * @response
     * {"data":{"title":"test1","start_time":1600590101,"end_time":1603182101,"cover":"[]","view_count":0,"vote_count":0,"user_count":2,"vote_mode":"cycle","vote_limit":1,"restricted_voting":0,"description":"asfasfasfsa","skin":{"nama":"","url":""},"color":"","sort":3,"sign_up":1,"position":"top","floating_objects":"","background_skin":"","border_style":"","mousic":{"name":"","url":""},"page_config":{"result":1,"comment":0,"top_rotation":0,"display_mode":2,"page_display":2,"home_page_number":32}},"msg":"Success","code":0}
     * 
     * @fields
     * - id | 活动id
     * - title | 标题
     * - start_time | 开始时间
     * - end_time | 结束时间
     * - qrcode | <b style="color:red">二维码</b>
     * - cover | <b style="color:red">轮播图，数组格式</b>
     * - vote_mode | 投票方式；fixed、固定的；cycle、周期性；
     * - vote_limit | 投票限制数量
     * - restricted_voting | 限定投票，0、关闭；1、开启
     * - description | 描述
     * - skin | 活动皮肤，对象类型，内置name、url字段
     * - color | 配色方案，颜色
     * - sort | 排序方式，1、参与时间倒叙；2、投票数从高到低；3、编号从低到高
     * - sign_up | 用户报名，0、关闭；1、开启
     * - position | 规则位置；top、顶部；bottom
     * - floating_objects | 漂浮物
     * - background_skin | 背景皮肤
     * - border_style | 边框样式
     * - mousic | 背景音乐，对象类型，内置name、url字段
     * - page_config | 页面配置，<a style="color:red" href="?path=Test&module=activity#page-config">对象类型</a>
     */
    public function actionView()
    {
        $id = $this->get('id');
        $activity = $this->activityService->detail($id, true);
        $fields = array_merge($this->fields, $this->voteConfigFields, $this->extraFields, $this->extraArrFields);
        $field = '{' . implode(',', $fields) . '}';
        $activity = $this->assemble(ActivityAssemble::class, $activity, $field);
        $this->output->setData($activity);
        return $this->output->getRowsOutput();
    }
}
