<?php

namespace app\modules\common\services;

use slr\graphql\src\Assemble;
use Yii;

abstract class BaseService
{
    use Assemble;
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * 不允许直接实例化
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 获取实例
     * @return static
     */
    public static function getInstance()
    {
        $cls = get_called_class();
        if (!array_key_exists($cls, static::$instances)) {
            static::$instances[$cls] = self::create();
        }
        return static::$instances[$cls];
    }


    /**
     * 创建实例
     * @return static
     */
    public final static function create()
    {
        return new static();
    }

    /**
     * 初始化方法
     */
    protected function init()
    {
        // 初始化方法
    }

    /**
     * 通过容器的方式获取服务
     * @param string $name
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getService(string $name)
    {
        return Yii::$container->get($name);
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

}