<?php

namespace app\modules\activity\controllers;

use app\modules\activity\services\ImageService;
use app\modules\common\assemble\activity\ImageAssemble;
use app\modules\common\controllers\AuthBaseController;

class ImageController extends AuthBaseController
{
    private $imageService;
    public function __construct($id, $module, ImageService $imageService,  $config = [])
    {
        $this->imageService = $imageService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 增加图片
     *
     * @http POST
     *
     *@alias activity/images
     * @params
     * - object_id | int | 图片关联对象id |  | Y
     * - type | int | 业务类型；1、活动的轮播图 |  | Y
     * - image | file | 图片，只上传一张，，image、url只能选其一 |  | N
     * - url | string | 选择选择链接，image、url只能选其一 |  | N
     *
     * @response
     * {"data":{"id":3},"msg":"Success","code":0}
     * 
     * @fields
     */
    public function actionCreate()
    {
        $objectId = $this->post('object_id', 0);
        $type = $this->post('type', 0);
        $url = $this->post('url', '');
        $image = $_FILES['image'] ?? [];
        
        $imageId = $this->imageService->create($this->userId, $type, $objectId, $url, $image);
        $data = [
            'id' => $imageId
        ];
        $this->output->setData($data);
        return $this->output->getRowsOutput();
    }


    /**
     * 投票
     *
     * @http POST
     *
     *@alias activity/votes
     * @params
     * - id | int | 图片id |  | Y
     * - image | file | 图片，只上传一张，，image、url只能选其一 |  | N
     * - url | string | 选择图片链接，image、url只能选其一 |  |  N
     *
     * @response
     * {"data":{},"msg":"Success","code":0}
     *
     * @fields
     */
    public function actionUpdate()
    {
        $id = $this->post('id', 0);
        $url = $this->post('url', '');
        $image = $_FILES['image'] ?? [];
        $this->imageService->update($id, $url, $image);
        return $this->output->getRowsOutput();
    }

    
    /**
     * 图片列表
     *
     * @http GET
     * @alias activity/images
     * 
     * @params
     * - object_id | int | 与图片关联的id，此处是活动id |  | Y
     * - type | int | 图片类型，次数是活动缩略图类型1 | 1 | Y
     * @response
     * {"data":{"list":[{"id":"2","url":"http://wb.dev.com/image/cover/1.png"},{"id":"3","url":"http://wb.dev.com/image/cover/1.png"}],"page":1,"page_size":20},"msg":"Success","code":0}
     * 
     * @fields
     * - id | 图片id
     * - url | 链接
     */
    public function actionIndex()
    {
        $objectId = $this->get('object_id', 0);
        $type = $this->get('type', 0);

        $images = $this->imageService->list($objectId, $type);
        $field = '{id,url}';
        $images = $this->assembleList(ImageAssemble::class, $images, $field);
        $this->outputList($images);
    }

    /**
     * 删除图片
     *
     * @http DELETE
     * @alias activity/images/1
     * 
     * @response
     * {"data":{},"msg":"Success","code":0}
     * 
     * @fields
     */
    public function actionDelete()
    {
        $id = $this->get('id');
        $this->imageService->deleteById($id);
        return $this->output->getRowsOutput();
    }
}
