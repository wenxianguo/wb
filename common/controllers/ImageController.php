<?php

namespace app\modules\common\controllers;

use app\exceptions\ServiceException;
use app\modules\common\services\ImageService;
use app\modules\sys\services\user\PassportService;
use app\response\ErrorCode;

class ImageController extends BaseController
{
    private $imageService;
    public function __construct($id, $module, ImageService $imageService, $config = [])
    {
        $this->imageService = $imageService;
        parent::__construct($id, $module, $config);
    }

    /**
     *上传图片
     * @http POST
     *
     * @alias /common/images
     * @params
     * - type | int | 来源页面：1、咨询类型；2、签约主体；3、可视化;4、tiktok 的营业执照；5、tiktok的备注描述；;6、google 的营业执照；7、google 的备注描述；8、fb oe 的备注描述；9、智能拼图;10、shopify | 1 | Y
     * - image | array | 图片说明| | Y
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionCreate()
    {
        $userId = (int)PassportService::getInstance()->authLogin();
        $fileInfo = $_FILES['image'] ?? [];
        $type = $this->post('type', 1);
        if (!$fileInfo) {
            ServiceException::send(ErrorCode::FB_FILE_IS_NOT_EXIST);
        }

        $id = $this->imageService->createImageTmp($userId, $type, $fileInfo);
        $data['image_id'] = $id;
        $this->output->setData($data);
        $this->output->getRowsOutput();
    }

    /**
     * 展示远端图片
     * @http GET
     *
     * @alias /common/images
     * @params
     * - path | string | 图片链接 |  | Y
     *
     * @response
     *
     * @fields
     */
    public function actionShow()
    {
        $imagePath = $this->get('path', '');

        $info = getimagesize($imagePath);

        header("content-type:" . $info['mime']);
        echo file_get_contents($imagePath);exit;
    }
}
