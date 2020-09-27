<?php
namespace app\modules\sys\services\verify;

use app\modules\common\lib\VerifyCode;
use app\modules\common\models\sys\SysCompanyGroupModel;
use app\modules\common\models\sys\SysCompanyModel;
use app\modules\common\models\sys\SysVerifyAddGroupUserModel;
use app\modules\common\models\sys\SysVerifyCodeModel;
use app\modules\common\services\OptLogService;
use app\response\ErrorCode;
use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysUserModel;
use Mobvista\MixTools\Src\Email\Email;
use Mobvista\MixTools\Src\Regex\RegexVali;



class AddGroupUserCodeService extends BaseVerifyService
{

    /**
     * 发送加入小组的邮件
     * @param $userId
     * @param $email
     * @param $url
     * @param array $data
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function sendAddGroupUserEmail($userId, $email, $url, array $data)
    {
        $this->filterAddGroupUserEmail($userId, $email, $data['company_id']);

        $type = SysVerifyCodeModel::TYPE_ADD_GROUP_USER_PAGE;

        $companyInfo = SysCompanyModel::find()->asArray()
            ->where(['id' => $data['company_id']])
            ->one();
        if (empty($companyInfo)) {
            throw new ServiceException('找不到企业数据');
        }
        $companyName = isset($companyInfo['company_name']) ? $companyInfo['company_name'] : '';


        if ($data['group_id'] > 0) {
            $groupInfo = SysCompanyGroupModel::find()->asArray()
                ->where(['id' => $data['group_id'], 'company_id' => $data['company_id']])
                ->one();
            if (empty($groupInfo)) {
                throw new ServiceException('找不到小组数据');
            }
        }
        $groupName = isset($groupInfo['group_name']) ? $groupInfo['group_name'] : '';

        $token = VerifyCode::randomKeys(10);
        $url = stripos($url, '?') === false ? $url . "?token={$token}" : $url . "&token={$token}";

        //注意，这里的文案有耦合，邮件的文案由后端定义，group有可能为空，意味用户只加入到企业而非企业的小组，但是邮件发出后用户"跳到是否加入的页面中"这页面的文案由前端判断group为不为空来显示
        if (!empty($groupName)) {
            $title = "[Mix]你正在加入到{$companyName}的{$groupName}小组";
            $content = "<p>您好，您正在加入到{$companyName}的{$groupName}小组，请点击以下链接确认，24小时内有效，请勿泄露：</p><p><a href='{$url}'>接受邀请</a></p>";
        } else {
            $title = "[Mix]你正在加入到{$companyName}";
            $content = "<p>您好，您正在加入到{$companyName}，请点击以下链接确认，24小时内有效，请勿泄露：</p><p><a href='{$url}'>接受邀请</a></p>";
        }

        $res = $this->sendEmailCode($type, Email::TYPE_ADD_GROUP_USER, $email, $token, $title, $content);
        if ($res) {
            $emailUserInfo = SysUserModel::find()->asArray()->where(['email' => $email, 'is_del' => 0])->orderBy('id desc')->limit(1)->one();

            $data = [
                'from_user_id' => $userId,
                'code' => $token,
                'company_id' => $data['company_id'],
                'group_id' => $data['group_id'],
                'receiver_email' => $email,
                'receiver_user_id' => $emailUserInfo['id'],
                'created_time' => time(),
            ];
            OptLogService::getInstance()->setCond('app\modules\common\models\sys\SysVerifyAddGroupUserModel', 'created')->setCreateNewValue($data)->log();
            SysVerifyAddGroupUserModel::insertData($data);
        }

        return true;
    }


    /**
     * 邮箱的验证条件
     * @param $userId
     * @param $email
     * @param $companyId
     * @return bool
     * @throws ServiceException
     */
    private function filterAddGroupUserEmail($userId, $email, $companyId)
    {
        if (!RegexVali::email($email)) {
            throw new ServiceException('请填写正确的邮箱', ErrorCode::EMAIL_ERROR);
        }

        $res = SysUserModel::find()->asArray()
            ->where(['is_del' => 0, 'email' => $email])
            ->orderBy('id desc')
            ->limit(1)
            ->one();
        if (empty($res)) {
            throw new ServiceException('此邮箱未注册', ErrorCode::EMAIL_NOT_USED);
        }
        if ($userId == $res['id']) {
            throw new ServiceException('不能发送到自己的邮箱地址', ErrorCode::GROUP_ADD_USER_SELF);
        }

        if (!empty($res['company_id']) && $res['company_id'] != $companyId) {
            throw new ServiceException('此邮箱已经绑定了其他企业', ErrorCode::GROUP_ADD_USER_COMPANY);
        }

        return true;
    }


}