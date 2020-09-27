<?php

namespace app\modules\common\controllers;

use app\exceptions\ServiceException;
use app\modules\common\enums\OssTypeEnum;
use app\modules\common\services\FileService;
use app\response\ErrorCode;

class FileController extends AuthBaseController
{
    private $fileService;
    public function __construct($id, $module, FileService $fileService, $config = [])
    {
        $this->fileService = $fileService;
        parent::__construct($id, $module, $config);
    }

    /**
     *上传图片
     * @http POST
     *
     * @alias /common/files
     * @params
     * - type | int | 来源页面：1、咨询类型；2、签约主体；3、可视化| 1 | Y
     * - fileType | string | 文件类型：image：图片，video：视频|  | Y
     * - file | array | 图片说明| | Y
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionCreate()
    {
        $fileInfo = $_FILES['file'] ?? [];
        $fileType = $this->post('fileType');
        $type = $this->post('type', 1);
        if (!$fileInfo) {
            ServiceException::send(ErrorCode::FB_FILE_IS_NOT_EXIST);
        }

        $id = $this->fileService->createFileTmp($this->userId, $type, $fileType, $fileInfo);
        if ($fileType == OssTypeEnum::IMAGE) {
            $data['image_id'] = $id;
        } else {
            $data['video_id'] = $id;
        }
        $this->output->setData($data);
        $this->output->getRowsOutput();
    }
}
