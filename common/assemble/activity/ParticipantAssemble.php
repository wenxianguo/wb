<?php
declare(strict_types=1);

namespace app\modules\common\assemble\activity;

use app\modules\common\assemble\BaseAssemble;

class ParticipantAssemble extends BaseAssemble
{
    public $assemble = [];

    public function getExtra()
    {
        return json_decode($this->assemble['extra']);
    }

    public function getCover()
    {
        $covers = json_decode($this->assemble['cover'], true);
        $data = [];
        foreach($covers as $cover) {
            $data[] = WB_API . $cover;
        }
        return $data;
    }
}