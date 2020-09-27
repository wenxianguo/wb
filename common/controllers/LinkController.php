<?php

namespace app\modules\common\controllers;

use app\modules\common\services\LinkService;

class LinkController extends AuthBaseController
{
    private $linkService;
    public function __construct($id, $module, LinkService $linkService, $config = [])
    {
        $this->linkService = $linkService;
        parent::__construct($id, $module, $config);
    }


    /**
     * 检验是否是shopify链接
     *
     * @http GET
     *
     *
     * @params
     *  - links | array | 字段 |  | Y
     *
     * @response
     * {"data":{"list":[{"link":"https://chicclubs.com/collections/hot-sales-buy-3-get-4th-free/products/men-hiking-pigskin-leather-slip-resistant-outdoor-casual-shoes-116746","is_shopify_link":1,"is_link":1}]},"msg":"Success","code":0}
     * @fields
     * - link | 链接
     * - is_shopify_link | 是否是shopify链接， 0、不是；1、是
     * - is_link | 是否是链接， 0、不是；1、是
     */
    public function actionVerifyShopifyLink()
    {
        $links = $this->post('links', []);
        $data = $this->linkService->verifyShopifyLinks($links);
        $this->output->setData($data);
        return $this->output->getListOutput();
    }
}
