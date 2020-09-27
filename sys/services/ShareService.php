<?php

namespace app\modules\sys\services;

use app\modules\common\lib\VerifyCode;
use app\modules\common\models\sys\SysShareModel;
use app\modules\sys\enums\ShareEnum;
use app\modules\sys\redis\ShareCache;

class ShareService
{
    private $shareModel;
    private $shareCache;
    public function __construct(SysShareModel $shareModel, ShareCache $shareCache)
    {
        $this->shareModel = $shareModel;
        $this->shareCache = $shareCache;
    }

    public function getPlanList(int $page, int $pageSize)
    {
        $offset = ($page - 1) * $pageSize;
        $list = $this->shareModel->getPlanList($offset, $pageSize);
        return $list;
    }

    public function planTotal()
    {
        return $this->shareModel->planTotal();
    }

    public function create(array $param)
    {
        $token = VerifyCode::randomStr(15);
        $param['token'] = $token;
        $param['created_time'] = time();
        $param['updated_time'] = time();
        $share = $this->shareModel->create($param);
        $this->updateShareConfig($param);
        return $share;
    }

    public function update(int $id, array $param)
    {
        $share = $this->shareModel->findById($id);
        $param['updated_time'] = time();
        $share = $this->shareModel->updateModel($share, $param);
        return $share;
    }

    public function getUserTokenByUserId(int $userId, string $username = '')
    {
        $share = $this->shareModel->findUserShareByUserId($userId);
        if (!$share) {
            $data['user_id'] = $userId;
            $data['promoter'] = $username;
            $data['type'] = ShareEnum::USER;
            $share = $this->create($data);
        }
        return $share['token'];
    }

    public function findByUserIds(array $userIds)
    {
        return $this->shareModel->findByUserIds($userIds);
    }

    public function findById(int $id)
    {
        return $this->shareModel->findById($id);
    }

    public function deleteById(int $id)
    {
        $this->shareModel->deleteById($id);
    }

    public function findByToken(string $token)
    {
        return $this->shareModel->findByToken($token);
    }

    public function likeByPromoter(string $promoter)
    {
        return $this->shareModel->likeByPromoter($promoter);
    }

    private function updateShareConfig(array $params)
    {
        if (isset($params['plan']) && $params['plan']) {
            $this->shareCache->zAddPlan($params['plan']);
        }
        if (isset($params['channel']) && $params['channel']) {
            $this->shareCache->zAddChannel($params['channel']);
        }
        if (isset($params['promoter']) && $params['promoter']) {
            $this->shareCache->zAddPromoter($params['promoter']);
        }
    }

    public function getPlans()
    {
        $data =  $this->shareCache->zrevrangePlan(0, 5);
        $plans = [];
        foreach ($data as $val) {
            $plans[] = [
                'id' => $val,
                'name' => $val
            ];
        }
        return $plans;
    }

    public function getChannels()
    {
        $data =  $this->shareCache->zrevrangeChannel(0, 5);
        $channel = [];
        foreach ($data as $val) {
            $channel[] = [
                'id' => $val,
                'name' => $val
            ];
        }
        return $channel;
    }

    public function getPromoters()
    {
        $data =  $this->shareCache->zrevrangePromoter(0, 5);
        $promoter = [];
        foreach ($data as $val) {
            $promoter[] = [
                'id' => $val,
                'name' => $val
            ];
        }
        return $promoter;
    }
}
