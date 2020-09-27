<?php

namespace app\modules\activity\controllers;

use app\modules\activity\services\VoteService;
use app\modules\common\controllers\BaseController;

class VoteController extends BaseController
{
    private $voteService;
    public function __construct($id, $module, VoteService $voteService,  $config = [])
    {
        $this->voteService = $voteService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 投票
     *
     * @http POST
     *
     *@alias activity/votes
     * @params
     * - activity_id | int | 活动id |  | Y
     * - participant_id | int | 参与者id |  | Y
     *
     * @response
     * {"data":{},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionCreate()
    {
        $activityId = $this->post('activity_id', 0);
        $participantId = $this->post('participant_id', 0);
        
        $openId = $_SERVER['REMOTE_ADDR'];
        $this->voteService->create($activityId, $participantId, $openId);
        return $this->output->getRowsOutput();
    }
}
