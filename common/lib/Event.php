<?php

namespace app\modules\common\lib;


class Event
{
    public static function trigger($class)
    {
        $name = $class->getNamespace();
        \Yii::$app->trigger($name, $class);
    }
}
