<?php

namespace app\modules\common\services;

use app\modules\common\enums\ConsultationTypeEnum;
use app\modules\common\events\ConsultationCreatedEvent;
use app\modules\common\lib\ElkLogType;
use app\modules\common\lib\Event;
use app\modules\common\models\common\ConsultationModel;
use BaseComponents\base\AliyunOss;
use Mobvista\MixTools\Src\Elk\Elk;

class ConsultationService
{
    const MAX_IMG_SIZE = 2 * 1024 * 1024;

    private $consultationModel;
    private $imageService;
    private $consultationCreatedEvent;
    public function __construct(
        ConsultationModel $consultationModel,
        ImageService $imageService,
        ConsultationCreatedEvent $consultationCreatedEvent
    )
    {
        $this->consultationModel = $consultationModel;
        $this->imageService = $imageService;
        $this->consultationCreatedEvent = $consultationCreatedEvent;
    }

    public function create(int $userId, int $type, string $description, string $phone, string $email, array $imageIds)
    {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'phone' => $phone,
            'email' => $email,
            'created_time' => time(),
            'updated_time' => time()
        ];
        $consultation = $this->consultationModel->create($data);
        $id = $consultation->id;
        $imagePaths = $this->imageService->operationImage($userId, $id, $imageIds);

        $this->createdEvent($userId, $description, $phone, $email, $imagePaths, $type);
    }

    private function createdEvent(int $userId, string $description, string $phone, string $email, array $images, int $type = ConsultationTypeEnum::OPEN_ACCOUNT_PAGE)
    {
        $this->consultationCreatedEvent->setUserId($userId);
        $this->consultationCreatedEvent->setDescription($description);
        $this->consultationCreatedEvent->setEmail($email);
        $this->consultationCreatedEvent->setPhone($phone);
        $this->consultationCreatedEvent->setImages($images);
        $this->consultationCreatedEvent->setType($type);

        Event::trigger($this->consultationCreatedEvent);
    }
}
