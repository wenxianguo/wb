<?php
declare(strict_types=1);

namespace app\modules\common\assemble\activity;

use app\modules\common\assemble\BaseAssemble;

class ImageAssemble extends BaseAssemble
{
    public $assemble = [];

    public function getUrl()
    {
        return WB_API . $this->assemble['path'];
    }
}