<?php

namespace app\modules\common\services;

use Mobvista\MixTools\Src\Elk\Elk;

abstract class InitService
{
    public $redis;

    public function __construct()
    {
        $this->redis = \Yii::$app->redis;
    }

    public function getOperateInfo(array $data, $info)
    {
        $oldUpdateContent = [];
        $newUpdateContent = [];
        foreach ($data as $key => $value) {
            if ($info[$key] != $value) {
                $newUpdateContent[$key] = $value;
                $oldUpdateContent[$key] = $info[$key];
            }
        }

        return [$oldUpdateContent, $newUpdateContent];
    }

    public function recordTime(float $startTime, float $endTime, int $count, string $type)
    {
        $params = [
            'time' => $endTime - $startTime,
            'count' => $count
        ];
        Elk::log($type, var_export($params, true), Elk::LEVEL_INFO);
    }

    public function recordTimes(array $data, string $type)
    {
        $params = [];
        $startTime = 0;
        foreach ($data as $key => $val) {
            if ($startTime) {
                $params[$key] = $val - $startTime;
            }
            $startTime = $val;
        }
        Elk::log($type, var_export($params, true), Elk::LEVEL_INFO);
    }
}