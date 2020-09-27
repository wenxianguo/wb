<?php
namespace app\modules\activity\services;

use app\exceptions\ServiceException;
use app\modules\activity\enums\ImageTypeEnum;
use app\modules\common\lib\QRcode;
use app\modules\common\models\activity\ActivityModel;
use app\response\ErrorCode;
use Yii;

class ActivityService
{
    private $activityModel;
    private $activityExtraService;
    private $intFields = [
        'vote_limit', 'restricted_voting', 'sort', 'sign_up', 
        'page_config' => ['result', 'display_mode', 'page_display', 'home_page_number', 'top_rotation', 'comment'],
        'player_setup' => ['number_limit', 'review', 'phone', 'address', 'description'],
        'prevent_swipe_tickets' => ['restricted_area','verification_code','black_list','pop_up_verification_code','everyday_number_limit','everyhour_number_limit'],
        'live_voting' => ['live_voting', 'outside_share'],
        'other_config' => ['vote_record']
    ];
    public function __construct(
        ActivityModel $activityModel, 
        ActivityExtraService $activityExtraService
        )
    {
        $this->activityModel = $activityModel;
        $this->activityExtraService = $activityExtraService;
    }

    /**
     * 创建活动操作
     *
     * @param integer $userId
     * @param string $title
     * @param integer $startTime
     * @param integer $endTime
     * @param string $description
     * @return int
     */
    public function create(int $userId, string $title, int $startTime ,int $endTime, string $description)
    {
        $params = $this->getCreateParams($userId, $title, $startTime, $endTime);
        $activity = $this->activityModel->create($params);
        $activityId = $activity->id;
        $this->activityExtraService->initCreate($userId, $activityId, $description);
        $this->addImage($userId, $activityId);
        $this->createQrcode($activityId);
        return $activityId;
    }

    /**
     * 复制活动
     *
     * @param integer $userId
     * @param integer $activityId
     * @return int
     */
    public function replace(int $userId, int $activityId)
    {
        $activity = $this->activityModel->findById($activityId);
        if($activity && $activity['user_id'] != $userId) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '活动不存在');
        }
        $activityArray = $activity->toArray();
        
        unset($activityArray['id']);
        $activityArray['title'] .= '(复制)';
        $activityArray['created_time'] = time();
        $newActivity = $this->activityModel->create($activityArray);
        $newActivityId = (int)$newActivity->id;
        $this->activityExtraService->replace($userId, $activityId, $newActivityId);
        
        return $newActivityId;
    }

    public function update(int $userId, int $id, array $params, array $voteConfig, array $extraParams, array $extraArrParams)
    {
        $activity = $this->activityModel->findById($id);
        $this->checkActivity($userId, $activity);
        $oldVoteConfig = json_decode($activity->vote_config, true);
        $voteConfig = array_merge($oldVoteConfig, $voteConfig);
        $params['vote_config'] = json_encode($voteConfig);
        $this->updateData($activity, $params);
        if($extraParams || $extraArrParams) {
            $this->activityExtraService->update($userId, $id, $extraParams, $extraArrParams);
        }
    }

    public function updateData($activity, $params)
    {
        $this->activityModel->updateModel($activity, $params);
    }

    public function detail(int $id, bool $isFront = false)
    {
        $activity = $this->activityModel->findById($id);
        if(!$activity) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '内容为空');
        }
        if($isFront) {
            $this->incrViewCount($activity);
        }
        $activityExtra = $this->activityExtraService->findByActivityId($id);
        $activity = $activity->toArray();
        $voteConfig = json_decode($activity['vote_config'], true);
        $extraParams = json_decode($activityExtra->params, true);
        $activity = array_merge($activity, $voteConfig, $extraParams);
        $activity = $this->parseField($activity);
        return $activity;
    }


    public function findById(int $id)
    {
        return $this->activityModel->findById($id);
    }

    public function list(int $userId, int $page = 1, int $pageSize = 20)
    {
        $total = $this->activityModel->totalByUserId($userId);
        $offset = ($page - 1) * $pageSize;
        $activities = [];
        if($total > $offset) {
            $activities = $this->activityModel->findByUserId($userId, $offset, $pageSize);
        }
        $data = [
            'list' => $activities,
            'total' => $total
        ];
        return $data;
    }

    /**
     * 清空浏览数
     *
     * @param integer $userId
     * @param integer $activityId
     * @return void
     */
    public function emptyViewCount(int $userId, int $activityId)
    {
        $activity = $this->activityModel->findById($activityId);
        $this->checkActivity($userId, $activity);
        $data['view_count'] = 0;
        $this->updateData($activity, $data);
    }

    /**
     * 清空投票数
     *
     * @param integer $userId
     * @param integer $activityId
     * @return void
     */
    public function emptyVoteCount(int $userId, int $activityId)
    {
        $activity = $this->activityModel->findById($activityId);
        $this->checkActivity($userId, $activity);
        $data['vote_count'] = 0;
        $this->updateData($activity, $data);
    }

    /**
     * 
     *
     * @param [type] $activity
     * @return void
     */
    private function incrViewCount($activity)
    {
        $data['view_count'] = $activity->view_count + 1;
        $this->updateData($activity, $data);
    }

    public function incrUserCount(int $id)
    {
        $activity = $this->activityModel->findById($id);
        $data['user_count'] = $activity->user_count + 1;
        $this->updateData($activity, $data);
    }

    public function decrUserCount(int $id)
    {
        $activity = $this->activityModel->findById($id);
        $data['user_count'] = $activity->user_count - 1;
        $this->updateData($activity, $data);
    }

    private function addImage(int $userId, int $activityId)
    {
        /**@var ImageService $imageService */
        $imageService = Yii::$container->get(ImageService::class);
        $path = '/image/cover/banner.jpg';
        $imageService->storage($userId, ImageTypeEnum::ACTIVITY_COVER, $activityId, $path);
    }

    private function createQrcode(int $activityId)
    {
        $url = WB_API . 'activity/front-activities/' .$activityId;
        $outFile = \Yii::$app->basePath . '/web/image/qrcode/activity_' . $activityId .'.png';
        QRcode::png($url, $outFile, QR_ECLEVEL_L);
    }

    private function parseField(array $activitys)
    {
        foreach($this->intFields as $key => $val) {
            if(is_array($val)) {
                foreach ($val as $k) {
                    $activitys[$key][$k] = (int)$activitys[$key][$k];
                }
            } else {
                $activitys[$val] = (int)$activitys[$val];
            }
        }
        return $activitys;
    }

    private function getCreateParams(int $userId, string $title, int $startTime ,int $endTime)
    {
        $voteConfig = [
            'vote_mode' => 'cycle',
            'vote_limit' => 1,
            'restricted_voting' => 0,
        ];
        $cover = [];
        $params = [
            'user_id' => $userId,
            'title' => $title,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'user_count' => 2,
            'cover' => json_encode($cover),
            'vote_config' => json_encode($voteConfig),
            'created_time' => time()
        ];
        return $params;
    }

    private function checkActivity(int $userId, $activity)
    {
        if($activity && $activity['user_id'] != $userId) {
            ServiceException::send(ErrorCode::CONTENT_IS_EMPTY, '活动不存在');
        }
    }
}
