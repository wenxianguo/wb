<?php
declare(strict_types=1);
namespace app\modules\common\services;

use app\exceptions\ServiceException;
use app\modules\common\enums\OssTypeEnum;
use app\response\ErrorCode;

class FileService
{
    private $imageService;
    private $videoService;
    public function __construct(ImageService $imageService, VideoService $videoService)
    {
        $this->imageService = $imageService;
        $this->videoService = $videoService;
    }


    public function createFileTmp(int $userId, int $type, string $fileType, array $fileInfo)
    {
        $id = 0;
        if ($fileType == OssTypeEnum::IMAGE) {
            $id = $this->imageService->createImageTmp($userId, $type, $fileInfo);
        } elseif ($fileType == OssTypeEnum::VIDEO) {
            $id = $this->videoService->createVideoTmp($userId, $type, $fileInfo);
        } else {
            ServiceException::send(ErrorCode::FILE_TYPE_IS_NOT_EXIST);
        }
        return $id;
    }
}
