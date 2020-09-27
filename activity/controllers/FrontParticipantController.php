<?php

namespace app\modules\activity\controllers;

use app\modules\activity\services\FrontParticipantService;
use app\modules\common\assemble\activity\ParticipantAssemble;
use app\modules\common\controllers\BaseController;

class FrontParticipantController extends BaseController
{
    private $frontParticipantService;
    public function __construct($id, $module, FrontParticipantService $frontParticipantService,  $config = [])
    {
        $this->frontParticipantService = $frontParticipantService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 参与者的字段
     *
     * @http GET
     *  @params
     * - activity_id | int | 活动id |  | Y
     * @response
     * {"data":[{"nickname":"姓名","key":"name","type":2},{"nickname":"描述","key":"description","type":1},{"nickname":"手机","key":"phone","type":1},{"nickname":"地址","key":"address","type":1},{"nickname":"test","key":"test_0","type":"1"},{"nickname":"test1","key":"test_1","type":"1"},{"nickname":"上传图片（1-3张）","key":"cover","type":2,"limit":3}],"msg":"Success","code":0}
     * 
     * @fields
     * - nickname | 页面显示昵称
     * - key | 表单提交时的key
     * - type | 验证类型，1、选填、2、必填
     * - limit | 图片上传的数量限制
     */
    public function actionPlayerField()
    {
        $activityId = $this->get('activity_id', 0);
        $fields = $this->frontParticipantService->getPlayerField($activityId);
        $this->output->setData($fields);
        return $this->output->getRowsOutput();
    }

    /**
     * 添加参与者
     *
     * @http POST
     * @alias activity/front-participants
     * 
     * @params
     * - activity_id | int | 活动id |  | Y
     * - name | string | 名称 |  | Y
     * - description | string | 描述 |  | N
     * - phone | string | 手机号码 |  | N
     * - address | string | 地址 |  | N
     * - cover | array | 图片，可以上传多个图片 |  | N
     * - test_1 | string | 额外字段 |  | N
     * 
     * @response
     * {"data":{"id":13},"msg":"Success","code":0}
     */
    public function actionCreate()
    {
        $params = $this->getCreateParams();
        $images = $_FILES['cover'] ?? [];
        $id = $this->frontParticipantService->create($params, $images);
        $data['id'] = $id;
        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }

     /**
     * 获取用户列表
     *
     * @http GET
     * @alias activity/front-participants
     * @params
     * - activity_id | int | 活动id |  | Y
     * - number | int | 编号 |  | N
     * - name | string | 名称 |  | N
     * - sort | int | 排序方式，1、参与时间倒叙；2、投票数从高到低；3、编号从低到高
     * - page | int | 页数 | 1 | N
     * - page_size | int | 每页行数 | 20 | N
     * @response
     * {"data":{"list":[{"id":"62","number":"1","name":"默认选项","phone":"","cover":["http://slr-apidev.mobvista.com/storage/upload/1601189559905739.png"],"vote_count":"0","status":"2","extra":""},{"id":"63","number":"2","name":"默认选项","phone":"","cover":"","vote_count":"0","status":"2","extra":""},{"id":"64","number":"3","name":"wenxg","phone":"1531354","cover":["http://slr-apidev.mobvista.com/storage/upload/1601190302589624.jpeg","http://slr-apidev.mobvista.com/storage/upload/160119030258997.jpeg"],"vote_count":"0","status":"1","extra":[{"nickname":"测试1","value":"hello world11"},{"nickname":"测试2","value":"asfas"}]},{"id":"65","number":"4","name":"wenxg1","phone":"1531354","cover":["http://slr-apidev.mobvista.com/storage/upload/1601190657344764.jpeg","http://slr-apidev.mobvista.com/storage/upload/1601190657344356.jpeg"],"vote_count":"0","status":"2","extra":[{"nickname":"测试1","value":"hello world11"},{"nickname":"测试2","value":"asfas"}]}],"total":4,"page":1,"page_size":20},"msg":"Success","code":0}
     * 
     * @fields
     * - id | 参数用户的自增id
     * - number | 编码
     * - name | 名称
     * - phone | 手机号码
     * - vote_count | 投票数
     * - extra | 扩展
     */
    public function actionIndex()
    {
        $params = $this->getParams();
        $sort = $this->get('sort', 0);
        $data = $this->frontParticipantService->list($params, $sort, $this->offset, $this->pageSize);
        $field = '{id,number,cover,name,phone,vote_count,extra}';
        $list = $this->assembleList(ParticipantAssemble::class, $data['list'], $field);
        $this->outputList($list, $data['total']);
    }

    /**
     * 获取参与者详情
     *
     * @http GET
     * @alias activity/participants/1
     * 
     * @params
     * @response
     *{"data":{"id":65,"number":4,"name":"wenxg1","phone":"1531354","vote_count":0,"cover":["http://slr-apidev.mobvista.com/storage/upload/1601190657344764.jpeg","http://slr-apidev.mobvista.com/storage/upload/1601190657344356.jpeg"],"description":"asfasf","extra":[{"nickname":"测试1","value":"hello world11"},{"nickname":"测试2","value":"asfas"}]},"msg":"Success","code":0}
     * @fields
     * - id | 参数用户的自增id
     * - number | 编码
     * - name | 名称
     * - phone | 手机号码
     * - vote_count | 投票数
     * - cover | 图片
     * - description | 描述
     * - extra | 扩展
     */
    public function actionView()
    {
        $id = $this->get('id', 0);
        $participant = $this->frontParticipantService->findById($id);
        $field = '{id,number,name,phone,vote_count,cover,description,extra}';
        $participant = $this->assemble(ParticipantAssemble::class, $participant, $field);
        $this->output->setData($participant);
        return $this->output->getRowsOutput();
    }

    private function getParams()
    {
        $params = [
            'activity_id' => $this->get('activity_id', 0),
            'number' => $this->get('number', 0),
            'name' => $this->get('name', '', 'trim')
        ];
        return $params;
    }

    private function getCreateParams()
    {
        $params = [
            'activity_id' => $this->post('activity_id', 0),
            'name' => $this->post('name', '', 'trim'),
            'description' => $this->post('description', '', 'trim'),
            'phone' => $this->post('phone', '', 'trim'),
            'address' => $this->post('address', '', 'trim'),
        ];
        //获取扩展信息
        $extra = [];
        for($i = 0; $i < 10; $i++) {
            $key = 'test_' . $i;
            if($this->post($key, '')) {
                $extra[$i] = $this->post($key, '');
            }
        }
        $params['extra'] = $extra;
        return $params;
    }
}
