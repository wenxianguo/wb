<?php

namespace app\modules\common\controllers;

use slr\graphql\src\Assemble;
use Yii;
use yii\web\Controller;
use app\response\OutPut;

/**
 * 最基础的controller
 * Class BaseController
 * @package app\modules\common\controllers
 */
class BaseController extends Controller
{
    use Assemble;

    /**
     * 分页参数
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * 分页大小参数
     * @var int
     */
    protected $pageSize = 20;

    /**
     * 是否统计列表总数
     * @var null
     */
    protected $isReturnTotal = null;

    /**
     * 接口输出格式化
     * @var \app\response\OutPut
     */
    protected $output = null;


    public function init()
    {
        $this->output = new OutPut();
        $isReturnTotal = Yii::$app->request->isPost ? Yii::$app->request->post('is_return_total', null) : Yii::$app->request->get('is_return_total', null);
        $page = Yii::$app->request->isPost ? Yii::$app->request->post('page', 1) : Yii::$app->request->get('page', 1);
        $pageSize = Yii::$app->request->isPost ? Yii::$app->request->post('page_size', 20) : Yii::$app->request->get('page_size', 20);

        $this->isReturnTotal = $isReturnTotal;
        $this->page = (int)min(max($page, 1), 10000000);  //页数 最大1kw 防止mysql报错
        $this->pageSize = (int)min(max($pageSize, 1), 10000);  //分页最大1w个 防止mysql报错
        $this->offset = ($this->page - 1) * $this->pageSize;
    }

    /**
     * 获取get请求数据
     * @param string $name
     * @param null $defaultValue
     * @param string $filterMethod
     * @return array|mixed
     */
    public function get(string $name, $defaultValue = null, string $filterMethod = '')
    {
        $value = \Yii::$app->request->get($name, $defaultValue);
        if ($value !== null && $filterMethod) {
            $value = $filterMethod($value);
        }
        return $value;
    }

    /**
     * 获取post请求数据
     * @param string $name
     * @param null $defaultValue
     * @param string $filterMethod
     * @return array|mixed
     */
    public function post(string $name, $defaultValue = null, string $filterMethod = '')
    {
        $value = \Yii::$app->request->post($name, $defaultValue);
        if ($value !== null && $filterMethod) {
            $value = $filterMethod($value);
        }
        return $value;
    }

    public function outputList($list, $total = null, $otherData = null)
    {
        $this->output->setData($list);
        $this->output->setPage($this->page);
        $this->output->setPageSize($this->pageSize);
        $this->output->setTotal($total);
        $otherData && $this->output->setOtherData($otherData);
        return $this->output->getListOutput();
    }


    public function outputCount($total)
    {
        $this->output->setData(['total' => $total]);
        return $this->output->getRowsOutput();
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

    /**
     * 获取语言
     *
     * @return void
     */
    public function getLanguage() : string
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $acceptLanguage = explode(',', $acceptLanguage);
        $language = $acceptLanguage[0];
        return $language;
    }
}
