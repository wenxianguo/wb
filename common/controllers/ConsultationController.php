<?php

namespace app\modules\common\controllers;

use app\exceptions\ServiceException;
use app\modules\common\services\ConsultationService;
use app\response\ErrorCode;
use Mobvista\MixTools\Src\Regex\RegexVali;

class ConsultationController extends AuthBaseController
{
    private $consultationService;
    public function __construct($id, $module, ConsultationService $consultationService, $config = [])
    {
        $this->consultationService = $consultationService;
        parent::__construct($id, $module, $config);
    }

    /**
     *创建咨询
     * @http POST
     *
     * @alias /common/consultations
     * @params
     * - type | int | 来源页面：1、开户管理；2、充值记录；3、充值；4、开户未完成；5、其他 | 1 | Y
     * - description | string | 问题描述 | | Y
     * - phone | string | 联系电话 | | Y
     * - email | string | 联系邮箱| | Y
     * - image_ids | array | 临时表id| | N
     *
     * @response
     * {"data":{},"code":0,"msg":"Success"}
     *
     * @fields
     */
    public function actionCreate()
    {

        $description = $this->post('description', '');
        $phone = $this->post('phone', '');
        $email = $this->post('email', '');
        $type = $this->post('type', 1);
        $imageIds = $this->post('image_ids', []);
        $this->filterParams($phone, $email);

        $this->consultationService->create($this->userId, $type, $description, $phone, $email, $imageIds);
        $this->output->getRowsOutput();
    }

    /**
     * 过滤电话号码、邮箱
     * @param string $phone
     * @param string $email
     * @throws ServiceException
     */
    private function filterParams(string $phone, string $email)
    {
        if (empty($phone)) {
            ServiceException::send(ErrorCode::PHONE_IS_EMPTY);
        }

        if (empty($email)) {
            ServiceException::send(ErrorCode::EMAIL_IS_EMPTY);
        }
        if(preg_match('|[\x{4e00}-\x{9fa5}]+?|u',$phone)){
            ServiceException::send(ErrorCode::PHONE_ERROR);
        }

        if (!RegexVali::email($email)) {
            ServiceException::send(ErrorCode::EMAIL_ERROR);
        }
    }
}
