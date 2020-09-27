<?php
namespace app\modules\activity\services;

use app\modules\activity\enums\VoteEnum;
use app\modules\activity\redis\VoteCache;
use app\modules\common\models\activity\VoteModel;

class VoteService
{
    private $voteModel;
    private $activityService;
    private $voteCache;
    private $participantService;
    public function __construct(VoteModel $voteModel, ActivityService $activityService, VoteCache $voteCache, ParticipantService $participantService)
    {
        $this->voteModel = $voteModel;
        $this->activityService = $activityService;
        $this->voteCache = $voteCache;
        $this->participantService = $participantService;
    }

    /**
     * 操作投票
     *
     * @param integer $activityId
     * @param integer $participantId
     * @param string $openId
     * @return void
     */
    public function create(int $activityId, int $participantId, string $openId)
    {
        $activity = $this->activityService->findById($activityId);
        $voteConfig = json_decode($activity->vote_config, true);
        //如果是周期性，则再redis查看投票数
        if($voteConfig['vote_mode'] == VoteEnum::TYPE_CYCLE) {
            $count = $this->voteCache->getVote($activityId, $openId);
            //如果用户投票数小于系统限制，则继续投票
            if($count < $voteConfig['vote_limit']) {
                $this->voteCache->incrVote($activityId, $openId);
                $this->storage($activity, $activityId, $participantId, $openId);
            }
        } else {
            $count = $this->voteModel->totalByActivityIdIdAndOpenId($activityId, $openId);
            if($count < $voteConfig['vote_limit']) {
                $this->storage($activity, $activityId, $participantId, $openId);
            }
        }
    }

    /**
     * 存储
     *
     * @param integer $activityId
     * @param integer $participantId
     * @param string $openId
     * @return void
     */
    private function storage($activity, int $activityId, int $participantId, string $openId)
    {
        $params = $this->getParams($activityId, $participantId, $openId);
        $this->voteModel->create($params);
        $this->updateActivityVoteCount($activity);
        $this->updateParticipantVoteCount($participantId);
    }

    /**
     * 更新活动的投票数
     *
     * @param [type] $activity
     * @return void
     */
    private function updateActivityVoteCount($activity)
    {
        $voteCount = $activity->vote_count;
        $data['vote_count'] = $voteCount + 1;
        $this->activityService->updateData($activity, $data);
    }

    /**
     * 更新参与者的投票数
     *
     * @param [type] $participantId
     * @return void
     */
    private function updateParticipantVoteCount($participantId)
    {
        $participant = $this->participantService->findById($participantId);
        $voteCount = $participant->vote_count;
        $data['vote_count'] = $voteCount + 1;
        $this->participantService->updateData($participant, $data);
    }

    private function getParams(int $activityId, int $participantId, string $openId)
    {
        $params = [
            'activity_id' => $activityId,
            'participant_id' => $participantId,
            'open_id' => $openId,
            'created_time' => time()
        ];
        return $params;
    }
}
