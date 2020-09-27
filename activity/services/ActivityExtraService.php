<?php
namespace app\modules\activity\services;

use app\exceptions\ServiceException;
use app\modules\activity\enums\ActivityEnum;
use app\modules\common\models\activity\ActivityExtraModel;
use app\response\ErrorCode;

class ActivityExtraService
{
    private $activityExtraModel;
    public function __construct(ActivityExtraModel $activityExtraModel)
    {
        $this->activityExtraModel = $activityExtraModel;
    }

    /**
     * 初始化扩展信息
     *
     * @param integer $userId
     * @param integer $activityId
     * @param string $description
     * @return void
     */
    public function initCreate(int $userId, int $activityId ,string $description)
    {
        $params = $this->getCreateParams($userId, $activityId, $description);
        $this->activityExtraModel->create($params);
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
        $activityExtra = $this->activityExtraModel->findByActivityId($oldActivityId);
        $this->checkActivity($userId, $activityExtra);
        $activityExtra = $activityExtra->toArray();
        unset($activityExtra['id']);
        $activityExtra['activity_id'] = $newActivityId;
        $activityExtra['created_time'] = time();
        $this->activityExtraModel->create($activityExtra);
    }

    /**
     * 更新数据
     *
     * @param integer $userId
     * @param integer $activityId
     * @param array $extraParams
     * @param array $extraArrParams
     * @return void
     */
    public function update(int $userId, int $activityId, array $extraParams, array $extraArrParams)
    {
        $activityExtra = $this->activityExtraModel->findByActivityId($activityId);
        $this->checkActivity($userId, $activityExtra);
        $params = json_decode($activityExtra->params, true);
   
        foreach($extraArrParams as $field => $value) {
            $params[$field] = array_merge($params[$field], $value);
        }
        $params = array_merge($params, $extraParams);
        $data['params'] = json_encode($params);
        $this->activityExtraModel->updateModel($activityExtra, $data);
    }

    public function findByActivityId(int $activityId)
    {
        return $this->activityExtraModel->findByActivityId($activityId);
    }

    private function getCreateParams(int $userId, int $activityId, string $description)
    {
        $playerSetup = [
            'name' => '姓名',
            'number_limit' => 3,
            'review' => ActivityEnum::CLOSE,
            'phone' => ActivityEnum::VERIFICATION_OPTIONAL,
            'address' => ActivityEnum::VERIFICATION_OPTIONAL,
            'description' => ActivityEnum::VERIFICATION_OPTIONAL,
            'extra' => []
        ];
        $preventSwipeTickets = [
            'restricted_area' => ActivityEnum::CLOSE,
            'verification_code' => ActivityEnum::CLOSE,
            'black_list' => ActivityEnum::OPEN,
            'pop_up_verification_code' => ActivityEnum::OPEN,
            'everyday_number_limit' => 1000,
            'everyhour_number_limit' => 300,
        ];
        $LiveVoting = [
            'live_voting' => ActivityEnum::CLOSE,
            'outside_share' => ActivityEnum::OPEN,
            'url' => ''
        ];
        $otherConfig = [
            'bottom_text' => '',
            'bottom_url' => '',
            'vote_record' => ActivityEnum::CLOSE,

        ];
        $pageConfig = [
            'result' => ActivityEnum::OPEN,
            'comment' => ActivityEnum::CLOSE,
            'top_rotation' => ActivityEnum::CLOSE,
            'display_mode' => ActivityEnum::DISPLAY_DOUBLE_ROW,
            'page_display' => ActivityEnum::PAGE_SYMMETRIC,
            'home_page_number' => 32,
        ];
        $extraInfo = [
            'description' => $description,
            'skin' => [
                'nama' => '',
                'url' => ''
            ],
            'color' => '',
            'sort' => ActivityEnum::SORT_NUMBER_ASC,
            'sign_up' => ActivityEnum::OPEN,
            'position' => 'top',
            'floating_objects' => '',
            'background_skin' => '',
            'border_style' => '',
            'music' => [
                'name' => '',
                'url' => ''
            ],
            'restricted_voting' => 0,
            'page_config' => $pageConfig,
            'player_setup' => $playerSetup,
            'prevent_swipe_tickets' => $preventSwipeTickets,
            'live_voting' => $LiveVoting,
            'other_config' => $otherConfig,
        ];
        $params = [
            'user_id' => $userId,
            'activity_id' => $activityId,
            'params' => json_encode($extraInfo),
            'created_time' => time()
        ];
        return $params;
    }

    private function checkActivity(int $userId, $activityExtra)
    {
        if(!$activityExtra || $activityExtra->user_id != $userId) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '内容不存在');
        }
    }
}
