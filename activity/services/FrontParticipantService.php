<?php
namespace app\modules\activity\services;

use app\exceptions\ServiceException;
use app\modules\activity\enums\ActivityEnum;
use app\modules\activity\enums\ParticipantStatusEnum;
use app\modules\common\lib\Image;
use app\modules\common\models\activity\ParticipantModel;
use app\response\ErrorCode;

class FrontParticipantService
{
    private $participantModel;
    private $activityService;
    private $activityExtraService;
    const NORMAL_FIELD = ['description' => '描述', 'phone' => '手机', 'address' => '地址'];
    public function __construct(ParticipantModel $participantModel, ActivityService $activityService, ActivityExtraService $activityExtraService)
    {
        $this->participantModel = $participantModel;
        $this->activityService = $activityService;
        $this->activityExtraService = $activityExtraService;
    }

    /**
     * 添加参与者
     *
     * @param array $data
     * @return int
     */
    public function create(array $data, array $images)
    {
        $activityId = $data['activity_id'];
        $maxNumber = $this->participantModel->getMaxNumberByActivityId($activityId);
        $activityExtra = $this->activityExtraService->findByActivityId($activityId);
        
        if(!$activityExtra) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '活动不存在');
        }
        $params = json_decode($activityExtra->params, true);
        if($params['sign_up'] != ActivityEnum::OPEN) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '改活动不能报名');
        }

        //开启审核
        if($params['player_setup']['review'] == ActivityEnum::OPEN) {
            $data['status'] = ParticipantStatusEnum::NOT_REVIEWED;
        } else {
            //参赛人数加1
            $this->incrUserCount($activityId);
        }
        if($images) {
            $filePaths = Image::updateImages($images);
            $data['cover'] = json_encode($filePaths);
        }
        if($data['extra']) {
            $extra = [];
            foreach($data['extra'] as $index => $val) {
                $extra[] = [
                    'nickname' => $params['player_setup']['extra'][$index]['name'],
                    'value' => $val
                ];
            }
            $data['extra'] = json_encode($extra);
        }
        
        $number = (int)$maxNumber['number'] + 1;
        $data['number'] = $number;
        
        $participant = $this->participantModel->create($data);
        return $participant->id;
    }

    /**
     * 参与者列表
     *
     * @param array $params
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function list(array $params, int $sort = 0, int $offset = 0, int $limit = 20)
    {
        $where = $this->getCondition($params);
        $order = $this->getOrder($sort, $params['activity_id']);
        $total = $this->participantModel->totalBySearch($where);
        $participants = [];
        if($total > $offset) {
            $participants = $this->participantModel->findBySearch($where, $order, $offset, $limit);
        }
        $data = [
            'list' => $participants,
            'total' => $total
        ];
        return $data;
    }

    public function findById(int $id)
    {
        return $this->participantModel->findById($id);
    }

    public function getPlayerField(int $activityId)
    {
        $activityExtra = $this->activityExtraService->findByActivityId($activityId);
        if(!$activityExtra) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '内容不存在');
        }
        $params = json_decode($activityExtra->params, true);
        $playerSetup = $params['player_setup'];
        $fields = [
            [
                'nickname' => $playerSetup['name'],
                'key' => 'name',
                'type' => ActivityEnum::VERIFICATION_REQUIRED
            ]
        ];
        foreach(self::NORMAL_FIELD as $field => $nikcname) {
            if($playerSetup[$field] != ActivityEnum::VERIFICATION_HIDE) {
                $fields[] = [
                    'nickname' => $nikcname,
                    'key' => $field,
                    'type' => $playerSetup[$field]
                ];
            }
        }
        foreach ($playerSetup['extra'] as $index => $value) {
            if($value['type'] != ActivityEnum::VERIFICATION_HIDE) {
                $fields[] = [
                    'nickname' => $value['name'],
                    'key' => 'test_' . $index,
                    'type' => $value['type']
                ];
            }
        }
        $fields[] = [
            'nickname' => '上传图片（1-3张）',
            'key' => 'cover',
            'type' => ActivityEnum::VERIFICATION_REQUIRED,
            'limit' => $playerSetup['number_limit']
        ];
        return $fields;
    }

    private function incrUserCount(int $activityId)
    {
        $this->activityService->incrUserCount($activityId);
    }

    /**
     * 获取排序规则
     *
     * @param string $sort
     * @return string
     */
    private function getOrder(string $sort = '', $activityId)
    {
        if (!$sort) {
            $activityExtra = $this->activityExtraService->findByActivityId($activityId);
            $extra = json_decode($activityExtra->params, true);
            $sort = $extra['sort'];
        } 
        $order = ActivityEnum::getMessage($sort);
        return $order;
    }

    private function getCondition(array $params)
    {
        $where = ['and'];
        $where[] = ['=', 'status', ParticipantStatusEnum::REVIEWED];
        if(isset($params['number']) && $params['number']) {
            $where[] = ['=', 'number', $params['number']];
        }
        if(isset($params['activity_id']) && $params['activity_id']) {
            $where[] = ['=', 'activity_id', $params['activity_id']];
        }
        if(isset($params['name']) && $params['name']) {
            $where[] = ['like', 'name', $params['name']];
        }
        return $where;
    }
}
