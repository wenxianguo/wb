<?php

namespace app\modules\activity\controllers;

use app\modules\common\assemble\activity\ParamAssemble;
use app\modules\common\controllers\AuthBaseController;

class ParamController extends AuthBaseController
{

     /**
     * 页面配置
     *
     * @alias activity/params
     * 
     * @fields
     * - skin | 皮肤，数组格式
     * - floating_objects  | 漂浮物，数组格式
     * - background_skin | 背景皮肤，数组格式
     * - border_style | 边框样式，数组格式
     * - music | 背景音乐，数组格式
     * - cover | 轮播图，数组格式
     */
    public function actionIndex()
    {
        $param = ['current_time' => time()];
        $field = '{skin,floating_objects,background_skin,border_style,music,cover}';
        $data = $this->assemble(ParamAssemble::class, $param, $field);

        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }
}
