<?php

namespace app\modules\common\assemble\sys;
use app\modules\common\assemble\BaseAssemble;

class UserAssemble extends BaseAssemble
{
    public $assemble = [];
   
    public function getId()
    {
        return (int)$this->assemble['id'];
    }
    public function getName()
    {
        return $this->assemble['user_name'];
    }
}