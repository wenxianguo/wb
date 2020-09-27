<?php

namespace app\modules\common\redis;


class BaseRedis
{
    const NAME = ['get', 'exists', 'hget'];
    /**
     * 对redis的方式的处理
     * 当接口存在slr_debug=1时清缓存
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        $redis = \Yii::$app->redis;
        $slrDebug = \Yii::$app->request->get('slr_debug');
        if (in_array($name, self::NAME) && $slrDebug == 1) {
            return null;
        }
        return call_user_func_array([$redis, $name], $arguments);
    }
}
