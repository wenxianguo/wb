<?php
namespace app\modules\common;
use yii\base\Module;

class Common extends Module
{
    public function init()
    {
        parent::init();
        $this->registerEvent();
    }

    /**
     * 注册事件
     */
    private function registerEvent()
    {
        foreach (EventProvider::BINDS as $event => $listeners) {
            foreach ($listeners as $listener) {
                $listenerClass = \Yii::$container->get($listener);
                \Yii::$app->on($event, [$listenerClass, 'handle']);
            }
        }
    }
}
