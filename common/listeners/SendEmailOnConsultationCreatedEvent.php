<?php

namespace app\modules\common\listeners;

use app\modules\common\enums\ConsultationTypeEnum;
use app\modules\common\events\ConsultationCreatedEvent;
use app\modules\common\lib\SlrEmail;
use app\modules\sys\enums\SysGlobalValKeyEnum;
use app\modules\sys\services\globalval\GlobalValService;
use app\modules\sys\services\user\UserService;
use Mobvista\MixTools\Src\Email\Email;

class SendEmailOnConsultationCreatedEvent
{
    private $user;
    private $emails;
    private $userService;
    private $globalValService;

    public function __construct(UserService $userService, GlobalValService $globalValService)
    {
        $this->userService = $userService;
        $this->globalValService = $globalValService;
    }

    public function handle(ConsultationCreatedEvent $consultationCreatedEvent)
    {
        $userId = $consultationCreatedEvent->getUserId();
        $description = $consultationCreatedEvent->getDescription();
        $phone = $consultationCreatedEvent->getPhone();
        $email = $consultationCreatedEvent->getEmail();
        $type = $consultationCreatedEvent->getType();
        $images = $consultationCreatedEvent->getImages();

        $this->sendMail($userId, $description, $phone, $email, $type, $images);
    }

    private function sendMail(int $userId, string $description, string $phone, string $email, int $type, array $images = [])
    {
        $emails = $this->getMails();
        $toEmailStr = $type == ConsultationTypeEnum::OTHER ? $emails['product_manager'] : $emails['consultation'];
        $toEmails = explode(',', $toEmailStr);

        $title = $this->getTitle($userId);
        $content = $this->getContent($userId, $description, $phone, $email, $type);
        $logType = Email::TYPE_CONSULTATION;
        $options = [];
        foreach ($images as $image) {
            $options['attachments'][] =  $image;
        }
        //给多个邮件账号发送邮件
        foreach ($toEmails as $toEmail) {
            SlrEmail::sendMail($toEmail, $title, $content, $logType, 'slr', $options);
        }
    }

    private function getMails()
    {
        if (empty($this->emails)) {
            $emails = $this->globalValService->getByKey(SysGlobalValKeyEnum::RECEIVE_EMAIL);
            $this->emails = $emails;
        }
        return $this->emails;
    }

    private function getTitle(int $userId)
    {
        $user = $this->getUser($userId);
        $userName = '';
        if ($user) {
            $userName = $user['user_name'];
        }
        $title = '用户反馈 - ' . $userName;
        return $title;
    }

    /**
     * 拼接邮箱内容
     * @param int $userId
     * @param string $description
     * @param string $phone
     * @param string $email
     * @param int $type
     * @return string
     */
    private function getContent(int $userId, string $description, string $phone, string $email, int $type)
    {
        $user = $this->getUser($userId);
        $userMail = '';
        if ($user) {
            $userMail = $user['email'];
        }
        $typeName = ConsultationTypeEnum::getMessage($type);
        $content = '<div style="font-size:14px;">';
        $content .= '<b>用户邮箱账号：</b>' . $userMail;
        $content .= '<br /><br /><b>来源页面：</b>' . $typeName;
        $content .= '<br /><br /><b>用户联系电话：</b>' . $phone;
        $content .= '<br /><br /><b>用户联系邮箱：</b>' . $email;
        $content .= '<br /><br /><b>问题描述：</b><br />' . $description;
        $content .= '</div>';

        return $content;
    }

    private function getUser(int $userId)
    {
        if (empty($this->user)) {
            $user = $this->userService->findById($userId);
            $this->user = $user;
        }
        return $this->user;
    }
}
