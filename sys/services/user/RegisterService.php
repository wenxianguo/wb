<?php

namespace app\modules\sys\services\user;

use app\modules\sys\enums\CompanyIdEnum;
use app\modules\sys\services\ShareService;
use app\modules\sys\services\verify\PageCodeService;
use app\modules\sys\services\verify\RegisterCodeService;
use app\modules\sys\services\verify\ResetPassCodeService;
use Mobvista\MixTools\Src\Ip\Ip;

class RegisterService
{
    private $registerCodeService;
    private $resetPassCodeService;
    private $userService;
    private $passportService;
    private $pageCodeService;
    private $userExtraService;
    private $shareService;
    public function __construct(
        RegisterCodeService $registerCodeService,
        ResetPassCodeService $resetPassCodeService,
        UserService $userService,
        PassportService $passportService,
        PageCodeService $pageCodeService,
        UserExtraService $userExtraService,
        ShareService $shareService
    )
    {
        $this->registerCodeService = $registerCodeService;
        $this->resetPassCodeService = $resetPassCodeService;
        $this->userService = $userService;
        $this->passportService = $passportService;
        $this->pageCodeService = $pageCodeService;
        $this->userExtraService = $userExtraService;
        $this->shareService = $shareService;
    }

    public function register($phone, string $password, string $code)
    {
        $this->registerCodeService->verifyRegPhone($phone, $code);
        $currentTime = time();
        $data = [
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'created_time' => $currentTime,
            'updated_time' => $currentTime,
            'last_login_time' => $currentTime,
            'last_login_ip' => ip2long(Ip::getClientIp())
        ];
        $userInfo = $this->userService->create($data);
        $userId = (int)$userInfo->id;
        $this->passportService->setLoginSession($userId);
        $userInfo = $this->passportService->getLoginInfo();
        return $userInfo;
    }

    public function sendRegEmail(string $email)
    {
        $this->registerCodeService->sendRegEmail($email);
    }

    public function sendResetPasswordEmail(string $email)
    {
        $this->resetPassCodeService->sendResetPasswordEmail($email);
    }

    public function resetPassword(string $phone, string $code, string $password)
    {
        $this->registerCodeService->verifyResetPasswordPhone($phone, $code);
        $userInfo = $this->userService->findByPhone($phone);
        $currentTime = time();
        $data = [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'updated_time' => $currentTime,
            'last_login_time' => $currentTime,
            'last_login_ip' => ip2long(Ip::getClientIp()),
        ];
        $this->userService->update($userInfo, $data);
    }

    public function verifyToken(string $token)
    {
        $this->pageCodeService->getPageStatus($token, PageCodeService::TYPE_RESET_EMAIL, false);
    }

    public function recordShare(int $userId, string $token)
    {
        $share = $this->shareService->findByToken($token);
        if (!$share) {
            return;
        }
        $data = [
            'share_id' => $share->id,
            'user_id' => $userId
        ];
        $this->userExtraService->update($userId, $data);
    }

}