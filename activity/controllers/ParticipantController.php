<?php

namespace app\modules\activity\controllers;

use app\modules\activity\services\ParticipantService;
use app\modules\common\assemble\activity\ParticipantAssemble;
use app\modules\common\controllers\AuthBaseController;

class ParticipantController extends AuthBaseController
{
    private $participantService;
    public function __construct($id, $module, 
    ParticipantService $participantService,  $config = [])
    {
        $this->participantService = $participantService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 添加用户
     *
     * @http POST
     * @alias activity/participants
     * 
     * @params
     * - activity_id | int | 活动id |  | Y
     * 
     * @response
     * {"data":{},"msg":"Success","code":0}
     */
    public function actionCreate()
    {
        $activityId = $this->post('activity_id');
        $this->participantService->create($this->userId, $activityId);
        return $this->output->getRowsOutput();
    }

     /**
     * 获取商店列表
     *
     * @http GET
     * @alias activity/participants
     * @params
     * - activity_id | int | 活动id |  | Y
     * - number | int | 编号 |  | N
     * - name | string | 名称 | 1 | N
     * - status | int | 状态；1、未审核；2、已审核 | 1 | Y
     * - sort | int | 排序方式，1、参与时间倒叙；2、投票数从高到低；3、编号从低到高
     * - page | int | 页数 | 1 | N
     * - page_size | int | 每页行数 | 20 | N
     * @response
     * {"data":{"list":[{"id":"36","number":"1","name":"默认选项","phone":"","vote_count":"0","status":"2","extra":""},{"id":"37","number":"2","name":"默认选项","phone":"","vote_count":"0","status":"2","extra":""}],"total":2,"page":1,"page_size":20},"msg":"Success","code":0}
     * 
     * @fields
     * - id | 参数用户的自增id
     * - number | 编码
     * - name | 名称
     * - phone | 手机号码
     * - vote_count | 投票数
     * - status | 状态；1、未审核；2、已审核
     * - extra | 扩展
     */
    public function actionIndex()
    {
        $params = $this->getParams();
        $data = $this->participantService->list($params, $this->offset, $this->pageSize);
        $field = '{id,number,name,phone,cover,vote_count,status,extra}';
        $list = $this->assembleList(ParticipantAssemble::class, $data['list'], $field);
        $this->outputList($list, $data['total']);
    }

    /**
     * 获取用户详情
     *
     * @http GET
     * @alias activity/participants/1
     * 
     * @params
     * @response
     * {"data":{"id":16,"number":2,"name":"wenxg","phone":"","vote_count":3,"status":1,"cover":"","description":"","extra":""},"msg":"Success","code":0}
     * 
     * @fields
     * - id | 参数用户的自增id
     * - number | 编码
     * - name | 名称
     * - phone | 手机号码
     * - vote_count | 投票数
     * - status | 状态；1、未审核；2、已审核
     * - cover | 图片
     * - description | 描述
     * - extra | 扩展
     */
    public function actionView()
    {
        $id = $this->get('id', 0);
        $participant = $this->participantService->detail($this->userId, $id);
        $field = '{id,number,name,phone,vote_count,status,cover,description,extra}';
        $participant = $this->assemble(ParticipantAssemble::class, $participant, $field);
        $this->output->setData($participant);
        return $this->output->getRowsOutput();
    }

    /**
     * 更新数据
     *
     * @http PUT
     * @alias activity/participants/1
     * 
     * @params
     * -status | int | 状态；1、未审核；2、已审核 |  | N
     * -add_vote_count | int | 增加的投票数 |  | N
     * -number | int | 编号 |  | N
     * -name | string | 名称 |  | N
     * -description | string | 描述 |  | N
     * 
     * @response
     * {"data":{},"msg":"Success","code":0}
     */
    public function actionUpdate()
    {
        $id = $this->get('id');
        $params = $this->getUpdateParams();
        
        $this->participantService->update($this->userId, $id, $params);
        return $this->output->getRowsOutput();
    }

       /**
     * 更新数据
     *
     * @http POST
     * 
     * @params
     * -participant_id | int | 参与者id |  | Y
     * -cover | array | 图片,可以同时上传多个图片 |  | Y
     * 
     * @response
     * {"data":{},"msg":"Success","code":0}
     */
    public function actionUpdateCover()
    {
        $participantId = $this->post('participant_id', 0);
        $cover = $_FILES['cover'] ?? [];
        $this->participantService->updateCover($this->userId, $participantId, $cover);
        return $this->output->getRowsOutput();
    }

    /**
     * 删除数据
     *
     * @http DELETE
     * @alias activity/participants/1
     * 
     * @params
     * 
     * @response
     * {"data":{},"msg":"Success","code":0}
     */
    public function actionDelete()
    {
        $id = $this->get('id');
        $this->participantService->delete($id); 
        return $this->output->getRowsOutput();
    }

    private function getUpdateParams()
    {
        $params = [];
        $fields = ['status', 'add_vote_count', 'name', 'number', 'description'];

        foreach($fields as $field) {
            if($this->post($field)) {
                $params[$field] = $this->post($field);
            }
        }
       
        return $params;
    }

    private function getParams()
    {
        $params = [
            'activity_id' => $this->get('activity_id', 0),
            'number' => $this->get('number', 0),
            'name' => $this->get('name', '', 'trim'),
            'status' => $this->get('status', 0),
            'sort' => $this->get('sort', 3),
        ];
        return $params;
    }
}
