<?php
namespace app\modules\common\traits;

use app\modules\sys\services\user\PassportService;

trait UserTrait
{
    /**
     * 获取整个项目的用户id
     *
     * @return void
     */
    public function getUserId()
    {
        /** @var McAppService $mcAppService */
        $mcAppService = $this->getService(McAppService::class);
        if ($mcAppService->hasSession()) {
            $userId = $mcAppService->getSession();
        } else {
            $userId = (int)PassportService::getInstance()->getLoginSession();
        }
    
        return $userId;
    }

    /**
     * 获取丝路的用户id
     *
     * @return int
     */
    public function getSlUserId()
    {
        $userId = (int)PassportService::getInstance()->getLoginSession();
        return $userId;
    }

    /**
     * 获取mc的用户id
     *
     * @return void
     */
    public function getMcUserId()
    {
        /** @var McAppService $mcAppService */
        $mcAppService = $this->getService(McAppService::class);
        $userId = 0;
        if ($mcAppService->hasSession()) {
            $userId = $mcAppService->getSession();
        }
        return $userId;
    }
}