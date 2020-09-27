<?php
declare(strict_types=1);
namespace app\modules\sys\services;

use app\modules\common\lib\Account;
use app\modules\common\models\sys\SysAccountModel;
 use app\modules\common\services\InitService;
 use app\modules\fb\services\OeService;
 use app\modules\sys\enums\AccountTypeEnum;
 use app\modules\sys\redis\AccountCache;
 use app\modules\google\services\OpenUserAccountService as GoogleOpenUserAccountService;
 use app\modules\tiktok\services\OpenUserAccountService as TiktokOpenUserAccountService;

 class AccountService extends InitService
 {
     private $sysAccountModel;
     private $accountCache;
     private $oeService;
     private $googleOpenUserAccountService;
     private $tiktokOpenUserAccountService;
     public function __construct(
         SysAccountModel $sysAccountModel,
         AccountCache $accountCache,
         OeService $oeService,
         GoogleOpenUserAccountService $googleOpenUserAccountService,
         TiktokOpenUserAccountService $tiktokOpenUserAccountService
     )
     {
         $this->sysAccountModel = $sysAccountModel;
         $this->accountCache = $accountCache;
         $this->oeService = $oeService;
         $this->googleOpenUserAccountService = $googleOpenUserAccountService;
         $this->tiktokOpenUserAccountService = $tiktokOpenUserAccountService;
     }

     /**
      * 获取各自模块中所有账号列表
      *
      * @param integer $userId
      * @param integer $type
      * @return void
      */
    public function getAccountsByUserIdAndType(int $userId, int $type = AccountTypeEnum::FB) :?array
    {
        //各自模块开的账户
        $service = $this->getServiceByType($type);
        $accounts = $service->getAccountsByUserId($userId);
        $accounts = array_column($accounts, null, 'account_id');

        //从3s中导过来的数据
        $sssAccounts = $this->findByUserIdAndType($userId, $type);
        $sssAccounts = array_column($sssAccounts, null, 'account_id');
        $accounts = $accounts + $sssAccounts;
        $this->formatAccounts($accounts, $type);
        return $accounts;
    }

     /**
      * 获取3s导过来的账号列表
      * @param int $userId
      * @param int $type
      * @return array|mixed|\yii\db\ActiveRecord[]
      */
     public function findByUserIdAndType(int $userId, int $type = AccountTypeEnum::FB)
     {
         $accounts = $this->accountCache->getAccountsByUserId($userId, $type);
         if (!$accounts) {
             $accounts = $this->sysAccountModel->findByUserIdAndType($userId, $type);
             $this->accountCache->setAccountsByUserId($userId, $type, $accounts);
         }
         return $accounts;
     }

     /**
      * 获取oe_account、open_account、sys_account的账号数据
      *
      * @param [type] $accountId
      * @param integer $userId
      * @param integer $type
      * @return void
      */
     public function findByAccountIdAndUserIdAndType($accountId, int $userId, int $type = AccountTypeEnum::FB)
     {
         $service = $this->getServiceByType($type);
         $account = $service->findByUserIdAndAccountId($userId, $accountId);
         if (!$account) {
             $account = $this->sysAccountModel->findByAccountIdAndUserIdAndType($accountId, $userId, $type);
         }
         return $account;
     }

     public function likeByAccountNameAndType(string $accountName, int $type = AccountTypeEnum::FB)
     {
         $sysAccounts = $this->sysAccountModel->likeByAccountNameAndType($accountName, $type);
         $service = $this->getServiceByType($type);
         $accounts = $service->likeByAccountName($accountName);
         $accounts = array_merge($accounts, $sysAccounts);
         $accountIds = [];
         if ($accounts) {
             $accountIds = array_unique(array_column($accounts, 'account_id'));
         }
         return $accountIds;

     }

     public function update(int $userId, int $accountId, string $accountName, int $type = AccountTypeEnum::FB)
     {
         $account = $this->sysAccountModel->findByAccountIdAndUserIdAndType($accountId, $userId, $type);
         if ($account) {
             $data['account_name'] = $accountName;
             $this->sysAccountModel->updateModel($account, $data);
             $this->accountCache->deleteAccountsByUserId($userId, $type);
         }
     }

     private function getServiceByType(int $type = AccountTypeEnum::FB)
     {
         switch ($type)
         {
             case AccountTypeEnum::FB:
                 $service = $this->oeService;
                 break;
             case AccountTypeEnum::TIKTOK:
                 $service = $this->tiktokOpenUserAccountService;
                 break;
             case AccountTypeEnum::GOOGLE:
                 $service = $this->googleOpenUserAccountService;
                 break;
             default:
                 $service = $this->oeService;
         }
         return $service;
     }

      /**
     * 处理google账号格式，将6660769698变成666-076-9698
     * @param array $accounts
     * @param int $type
     */
    private function formatAccounts(array &$accounts, int $type)
    {
        if ($type != AccountTypeEnum::GOOGLE) {
            return;
        }
        $accounts = array_map(function ($account){
            $accountId = Account::formatGoogle($account['account_id']);
            $account['account_id'] = $accountId;
            return $account;
        }, $accounts);
    }
 }