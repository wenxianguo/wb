<?php

namespace app\modules\common\assemble;

use app\modules\common\assemble\sys\UserAssemble;
use app\modules\sys\services\user\PassportService;
use app\modules\sys\services\user\UserExtraService;
use app\modules\sys\services\user\UserService;
use slr\graphql\src\Assemble;

class BaseAssemble
{
    use Assemble;
    public $assemble = [];
    public $userInfos = [];
    public $userExtraInfos = [];
    public $userId;
    public $language;

    /**
     * 存放登录session的名称
     */
    const SESSION_USER_ID = 'slr_user_id';

    public function __get($name)
    {
        $functionName = $this->getFunctionName($name);
        $getter = 'get' . $functionName;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }  else {
            return $this->assemble[$name];
        }
    }

    private function getFunctionName($name)
    {
        $arrName = explode('_', $name);
        $functionName = '';
        foreach ($arrName  as $value) {
            $functionName .= ucfirst($value);
        }
        return $functionName;
    }

    public function getService(string $name)
    {
        return \Yii::$container->get($name);
    }

    public function getSessionUserId()
    {
        if (!$this->userId) {
            $userId = (int)\Yii::$app->session->get(self::SESSION_USER_ID);
            $this->userId = PassportService::getInstance()->simulationLogin($userId);
        }
        return $this->userId;
    }

    public function getUser()
    {
        $userId = $this->getUserId();
        $user = $this->getUserInfo($userId);
        $user =$this->assemble(UserAssemble::class, $user);
        return $user;
    }

    public function getUserInfo(int $userId)
    {
        if (!isset($this->userInfos[$userId]) || !$this->userInfos[$userId]) {
            /** @var UserService $userService */
            $userService = $this->getService(UserService::class);
            $user = $userService->findById($userId);
            $this->userInfos[$userId] = $user;
        }
        return $this->userInfos[$userId];
    }

    public function getUserExtraInfo(int $userId)
    {
        if (!isset($this->userExtraInfos[$userId]) || !$this->userExtraInfos[$userId]) {
            /** @var UserExtraService $userExtraService */
            $userExtraService = $this->getService(UserExtraService::class);
            $user = $userExtraService->findByUserId($userId);
            $this->userExtraInfos[$userId] = $user;
        }
        return $this->userExtraInfos[$userId];
    }

    public function getUserId()
    {
        return (int)$this->assemble['user_id'];
    }


    public function getExtraField()
    {
        return '';
    }

    public function getCreatedTimeExcel()
    {
        return date('Y-m-d H:i:s', $this->assemble['created_time']);
    }


    public function getUpdatedTimeExcel()
    {
        $updatedTime = $this->assemble['updated_time'];
        $time = '';
        if ($updatedTime) {
            $time = date('Y-m-d H:i:s', $this->assemble['updated_time']);
        }
        return $time;
    }

    public function getUserExcel()
    {
        $userId = $this->getUserId();
        $user = $this->getUserInfo($userId);
        $data = '';
        if ($user) {
            $data = $user->user_name . '（' . $userId . '）';
        }
        return $data;
    }

    public function getUsernameExcel()
    {
        $userId = $this->getUserId();
        $user = $this->getUserInfo($userId);
        $username = $user ? $user->user_name : '';
        return $username;
    }

    public function getSssUserName()
    {
        $userId = $this->getUserId();
        $userExtraInfo = $this->getUserExtraInfo($userId);
        return $userExtraInfo ? $userExtraInfo->sss_user_name : '';
    }

    public function getLanguage()
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $acceptLanguage = explode(',', $acceptLanguage);
        $language = $acceptLanguage[0];
        return $language;
    }
}