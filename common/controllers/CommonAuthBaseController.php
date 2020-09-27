<?php
namespace app\modules\common\controllers;

use app\exceptions\ServiceException;
use app\modules\shopify\services\McAppService;
use app\modules\shopify\services\ShopifyUserProductService;
use app\modules\sys\services\user\PassportService;
use app\modules\sys\services\user\RoleService;
use app\response\ErrorCode;

/**
 * 权限和登录基础控制器
 * Class ProfileController
 * @package app\modules\sys\controllers\user
 */
class CommonAuthBaseController extends BaseController
{

    /**
     * 登录的用户id
     * @var
     */
    protected $userId = 0;

    protected $isShopify = 0;

    /**
     * 登录和权限验证
     * @throws \app\exceptions\ServiceException
     */
    public function init()
    {
        parent::init();
        /** @var McAppService $mcAppService */
        $mcAppService = $this->getService(McAppService::class);
        if ($mcAppService->hasSession()) {
            $this->userId = $mcAppService->getSession();
            $this->isShopify = 1;
        } else {
            $this->userId = (int)PassportService::getInstance()->getLoginSession();
        }
        if (!$this->userId) {
            ServiceException::send(ErrorCode::ERROR_NOT_LOGIN);
        }
        
    }

}