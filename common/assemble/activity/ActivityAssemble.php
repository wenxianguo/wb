<?php
declare(strict_types=1);

namespace app\modules\common\assemble\activity;

use app\modules\activity\enums\ImageTypeEnum;
use app\modules\activity\services\ImageService;
use app\modules\common\assemble\BaseAssemble;

class ActivityAssemble extends BaseAssemble
{
    public $assemble = [];
    private $imageService;
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getCover()
    {
        $id = $this->getId();
        $images = $this->imageService->list($id, ImageTypeEnum::ACTIVITY_COVER);
        $urls = [];
        foreach($images as $image) {
            $urls[] = WB_API . $image['path'];
        }
        return $urls;
    }

    public function getQrcode()
    {
        $id = $this->getId();
        $qrcode = WB_API . '/image/qrcode/activity_' . $id .'.png';
        return $qrcode;
    }

    public function getId()
    {
        return (int)$this->assemble['id'];
    }
}