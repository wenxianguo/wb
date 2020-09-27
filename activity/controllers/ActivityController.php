<?php

namespace app\modules\activity\controllers;

use app\modules\activity\services\ActivityService;
use app\modules\activity\services\ParticipantService;
use app\modules\common\assemble\activity\ActivityAssemble;
use app\modules\common\controllers\AuthBaseController;

class ActivityController extends AuthBaseController
{
    private $fields = ['title', 'start_time', 'end_time'];
    private $voteConfigFields = ['vote_mode', 'vote_limit', 'restricted_voting'];
    private $extraFields = ['description', 'skin', 'color', 'sort', 'sign_up', 'position',
        'floating_objects', 'background_skin', 'border_style', 'music'];
    private $extraArrFields = ['page_config', 'player_setup', 'prevent_swipe_tickets', 'live_voting', 'other_config'];

    private $activityService;
    private $participantService;
    public function __construct($id, $module,
        ActivityService $activityService, ParticipantService $participantService, $config = []) {
        $this->activityService = $activityService;
        $this->participantService = $participantService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 创建活动
     *
     * @http POST
     *
     * @alias activity/activities
     *
     * @params
     * - title | string | 标题 |  | Y
     * - start_time | int | 开始时间 |  | Y
     * - end_time | int | 结束时间 |  | Y
     * - description | string | 规则描述 |  | Y
     *
     * @response
     * {"data":{"id":13},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionCreate()
    {
        $title = $this->post('title', '', 'trim');
        $startTime = $this->post('start_time', 0);
        $endTime = $this->post('end_time', 0);
        $description = $this->post('description', '', 'trim');

        $id = $this->activityService->create($this->userId, $title, $startTime, $endTime, $description);
        $this->participantService->initCreate($this->userId, $id);
        $data['id'] = $id;
        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }

    /**
     * 复制活动
     *
     * @http POST
     *
     *
     * @params
     * - activity_id | int | 复制id |  | Y
     *
     * @response
     * {"data":{"id":13},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionReplace()
    {
        $activityId = $this->post('activity_id', 0);
        $id = $this->activityService->replace($this->userId, $activityId);
        $this->participantService->replace($this->userId, $activityId, $id);
        $data['id'] = $id;
        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }

    /**
     * 编辑，参数跟详情的返回值字段一致，太多懒得写了
     *
     * @http PUT
     *
     * @alias activity/activities/1
     *
     * @params
     *
     * @response
     * {"data":{"url":""},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionUpdate()
    {
        $id = $this->get('id');
        $params = $this->getParams();
        $voteConfig = $this->getVoteConfig();
        $extraParams = $this->getExtraParams();
        $extraArrParams = $this->getExtraArrParams();

        $this->activityService->update($this->userId, $id, $params, $voteConfig, $extraParams, $extraArrParams);
        return $this->output->getRowsOutput();
    }

    /**
     * 获取活动详情
     *
     * @http GET
     * @alias activity/activities/1
     *
     * @params
     * @response
     * {"title":"test4","start_time":1600590101,"end_time":1603182101,"vote_mode":"cycle","vote_limit":1,"restricted_voting":0,"description":"asfasfasfsa","skin":{"nama":"","url":""},"color":"","sort":3,"sign_up":1,"position":"top","floating_objects":"","background_skin":"","border_style":"","mousic":{"name":"","url":""},"page_config":{"result":1,"comment":0,"top_rotation":0,"display_mode":2,"page_display":2,"home_page_number":32},"player_setup":{"name":"姓名","number_limit":3,"review":0,"phone":1,"address":1,"description":1,"extra":[]},"prevent_swipe_tickets":{"restricted_area":0,"verification_code":0,"black_list":1,"pop_up_verification code":1,"everyday_number_limit":1000,"everyhour_number_limit":300},"Live_voting":{"live_voting":0,"outside_share":1,"url":""},"other_config":{"bottom_text":"","bottom_url":"","vote_record":0}}
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
     * - player_setup | 选手报名设置，<a style="color:red" href="?path=Test&module=activity#player-setup">对象类型</a>
     * - prevent_swipe_tickets | 防刷票设置，<a style="color:red" href="?path=Test&module=activity#prevent-swipe-tickets">对象类型</a>
     * - Live_voting | 现场投票设置，<a style="color:red" href="?path=Test&module=activity#live-voting">对象类型</a>
     * - other_config | 其他配置，<a style="color:red" href="?path=Test&module=activity#other-config">对象类型</a>
     */
    public function actionView()
    {
        $id = $this->get('id');
        $activity = $this->activityService->detail($id);
        $fields = array_merge($this->fields, ['cover', 'qrcode'], $this->voteConfigFields, $this->extraFields, $this->extraArrFields);
        $field = '{' . implode(',', $fields) . '}';
        $activity = $this->assemble(ActivityAssemble::class, $activity, $field);
        $this->output->setData($activity);
        return $this->output->getRowsOutput();
    }

    /**
     * 获取商店列表
     *
     * @http GET
     * @alias activity/activities
     * @params
     * - page | int | 页码 | 1 | N
     * - page_size | int | 每页行数 | 20 | N
     * @response
     *
     * @fields
     * - id | 投票id
     * - title | 标题
     * - qrcode | 二维码
     * - user_count | 参赛人数
     * - vote_count | 投票数
     * - view_count | 浏览数
     * - created_time | 创建时间
     */
    public function actionIndex()
    {
        $data = $this->activityService->list($this->userId, $this->page, $this->pageSize);
        $field = '{id,title,created_time,user_count,vote_count,view_count,cover,qrcode}';
        $list = $this->assembleList(ActivityAssemble::class, $data['list'], $field);
        $this->outputList($list, $data['total']);
    }

    /**
     * 清空浏览数
     *
     * @http POST
     *
     *
     * @params
     *
     * @response
     * {"data":{},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionEmptyViewCount()
    {
        $activityId = $this->post('activity_id', 0);
        $this->activityService->emptyViewCount($this->userId, $activityId);
        return $this->output->getRowsOutput();
    }

    /**
     * 清空投票数
     *
     * @http POST
     *
     *
     * @params
     *
     * @response
     * {"data":{},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionEmptyVoteCount()
    {
        $activityId = $this->post('activity_id', 0);
        $this->activityService->emptyVoteCount($this->userId, $activityId);
        $this->participantService->emptyVoteCount($activityId);
        return $this->output->getRowsOutput();
    }

    private function getParams()
    {
        $params = [];
        foreach ($this->fields as $field) {
            if ($this->post($field)) {
                $params[$field] = $this->post($field);
            }
        }
        return $params;
    }

    private function getExtraParams()
    {
        $params = [];
        foreach ($this->extraFields as $field) {
            if ($this->post($field)) {
                $params[$field] = $this->post($field);
            }
        }
        return $params;
    }

    private function getExtraArrParams()
    {
        $params = [];
        foreach ($this->extraArrFields as $field) {
            if ($this->post($field)) {
                $params[$field] = $this->post($field);
            }
        }
        return $params;
    }

    private function getVoteConfig()
    {
        $params = [];
        foreach ($this->voteConfigFields as $field) {
            if ($this->post($field)) {
                $params[$field] = $this->post($field);
            }
        }
        return $params;
    }
}
