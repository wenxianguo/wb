<?php
namespace app\modules\activity\services;

use app\exceptions\ServiceException;
use app\modules\activity\enums\ActivityEnum;
use app\modules\activity\enums\ParticipantStatusEnum;
use app\modules\common\lib\Image;
use app\modules\common\models\activity\ParticipantModel;
use app\response\ErrorCode;

class ParticipantService
{
    private $participantModel;
    private $activityService;
    public function __construct(
        ParticipantModel $participantModel, ActivityService $activityService
        )
    {
        $this->participantModel = $participantModel;
        $this->activityService = $activityService;
    }

    /**
     * 初始化参赛用户
     *
     * @param integer $userId
     * @param integer $activityId
     * @return void
     */
    public function initCreate(int $userId, int $activityId)
    {
        $params = [];
        for($i = 1; $i <= 2; $i++) {
            $params[] = $this->getInitParams($userId, $activityId, $i);
        }
        ParticipantModel::batchInsert($params);
    }

    /**
     * 复制操作
     *
     * @param integer $userId
     * @param integer $oldActivityId
     * @param integer $newActivityId
     * @return void
     */
    public function replace(int $userId, int $oldActivityId, int $newActivityId)
    {
        $participants = $this->participantModel->findByUserIdActivityId($userId, $oldActivityId);
        foreach($participants as &$participant) {
            unset($participant['id']);
            $participant['activity_id'] = $newActivityId;
            $participant['created_time'] = time();
        }
        ParticipantModel::batchInsert($participants);
    }

    public function create(int $userId, int $activityId)
    {
        $maxNumber = $this->participantModel->getMaxNumberByActivityId($activityId);
        $number = (int)$maxNumber['number'] + 1;
        $params = $this->getInitParams($userId, $activityId, $number);
        $this->incrUserCount($activityId);
        $this->participantModel->create($params);
    }

    public function list(array $params, int $offset = 0, int $limit = 20)
    {
        $where = $this->getCondition($params);
        $total = $this->participantModel->totalBySearch($where);
        $participants = [];
        if($total > $offset) {
            $sort = $params['sort'];
            $order = ActivityEnum::getMessage($sort);
            $participants = $this->participantModel->findBySearch($where, $order, $offset, $limit);
        }
        $data = [
            'list' => $participants,
            'total' => $total
        ];
        return $data;
    }

    public function detail(int $userId, int $id)
    {
        $participant = $this->findById($id);
        if(!$participant ||$participant->user_id != $userId) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '用户不存在');
        }
        return $participant;
    }

    public function findById(int $id)
    {
        return $this->participantModel->findById($id);
    }

    public function update(int $userId, int $id, array $params)
    {
        $participant = $this->participantModel->findById($id);
        $activityId = $participant->activity_id;
        $activity = $this->activityService->findById($activityId);
        if(!$participant || $activity->user_id != $userId) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '用户不存在');
        }
        
        if(isset($params['add_vote_count'])) {
            $participant->vote_count += $params['add_vote_count'];
            unset($params['add_vote_count']);
        }
        //更新活动的参数人数
        if(isset($params['status'])) {
            if($params['status'] == ParticipantStatusEnum::NOT_REVIEWED) {
                $this->decrUserCount($activityId);
            } else {
                $this->incrUserCount($activityId);
            }
        }
        $this->participantModel->updateModel($participant, $params);
    }

    public function updateCover(int $userId, int $participantId, array $cover)
    {
        $participant = $this->participantModel->findById($participantId);
        $activityId = $participant->activity_id;
        $activity = $this->activityService->findById($activityId);
        if(!$participant || $activity->user_id != $userId) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '用户不存在');
        }
        $data = [];
        if($cover) {
            $filePaths = Image::updateImages($cover);
            $data['cover'] = json_encode($filePaths);
        } 
        $this->updateData($participant, $data);
    }

    public function updateData($participant, $params)
    {
        $this->participantModel->updateModel($participant, $params);
    }

    public function delete(int $id)
    {
        $participant = $this->participantModel->findById($id);
        if($participant->status == ParticipantStatusEnum::REVIEWED) {
            $this->decrUserCount($participant->activity_id);
        }
        $this->participantModel->deleteById($id);
    }

    /**
     * 清空投票数
     *
     * @param integer $activityId
     * @return void
     */
    public function emptyVoteCount(int $activityId)
    {
        $participants = $this->participantModel->findByActivityId($activityId);
        $participantIds = array_column($participants, 'id');
        $data['vote_count'] = 0;
        $where['id'] = $participantIds;
        ParticipantModel::updateAll($data, $where);
    }

    
    private function incrUserCount(int $activityId)
    {
        $this->activityService->incrUserCount($activityId);
    }

    private function decrUserCount(int $activityId)
    {
        $this->activityService->decrUserCount($activityId);
    }

    private function getCondition(array $params)
    {
        $where = ['and'];
        if(isset($params['number']) && $params['number']) {
            $where[] = ['=', 'number', $params['number']];
        }
        if(isset($params['activity_id']) && $params['activity_id']) {
            $where[] = ['=', 'activity_id', $params['activity_id']];
        }
        if(isset($params['name']) && $params['name']) {
            $where[] = ['like', 'name', $params['name']];
        }
        if(isset($params['status']) && $params['status']) {
            $where[] = ['=', 'status', $params['status']];
        }
        return $where;
    }

    private function getInitParams(int $userId, int $activityId, int $number)
    {        
        $params = [
            'user_id' => $userId,
            'activity_id' => $activityId,
            'name' => '默认选项',
            'number' => $number,
            'description' => '',
            'cover' => '',
            'video' => '',
            'created_time' => time()
        ];
        return $params;
    }
}
