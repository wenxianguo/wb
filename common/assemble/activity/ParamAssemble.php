<?php
declare(strict_types=1);

namespace app\modules\common\assemble\activity;

use app\modules\common\assemble\BaseAssemble;
use app\modules\sys\enums\SysGlobalValKeyEnum;
use app\modules\sys\services\globalval\GlobalValService;
use Yii;

class ParamAssemble extends BaseAssemble
{
    public $assemble = [];
    private $globalValService;
    private $activityConfig;
    public function __construct(GlobalValService $globalValService)
    {
        $this->globalValService = $globalValService;
    }

    public function getSkin()
    {
        $skins = $this->getActivityConfig('skin');
        $skins = array_map(function($skin){
            $skin['url'] = WB_API . $skin['url'];
            return $skin;
        }, $skins);
        return $skins;
    }

    public function getFloatingObjects()
    {
        $floatingObjects = $this->getActivityConfig('floating_objects');
        $floatingObjects = array_map(function($floatingObject){
            $floatingObject['url'] = WB_API . $floatingObject['url'];
            return $floatingObject;
        }, $floatingObjects);
        return $floatingObjects;
    }

    public function getBackgroundSkin()
    {
        $backgroundSkins = $this->getActivityConfig('background_skin');
        $backgroundSkins = array_map(function($backgroundSkin){
            return WB_API . $backgroundSkin;
        }, $backgroundSkins);
        return $backgroundSkins;
    }

    public function getBorderStyle()
    {
        $borderStyles = $this->getActivityConfig('border_style');
        $borderStyles = array_map(function($borderStyle){
            return WB_API . $borderStyle;
        }, $borderStyles);
        return $borderStyles;
    }

    public function getMusic()
    {
        $musics = $this->getActivityConfig('music');
        $data = [];
        foreach($musics as $key => $val) {
            $data[] = [
                'name' => $key,
                'url' => WB_API . '/bgm/' . $val . '.mp3'
            ];
        }
        return $data;
    }

    public function getCover()
    {
        $covers = $this->getActivityConfig('cover');
        $covers = array_map(function($cover){
            return WB_API . $cover;
        }, $covers);
        return $covers;
    }

    private function getActivityConfig($name)
    {
        if(!$this->activityConfig) {
            $this->activityConfig = $this->globalValService->getByKey(SysGlobalValKeyEnum::ACTIVITY);
        }
        return $this->activityConfig[$name];
    }
}