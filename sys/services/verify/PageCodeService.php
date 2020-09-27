<?php
namespace app\modules\sys\services\verify;

use app\modules\common\models\sys\SysCompanyGroupModel;
use app\modules\common\models\sys\SysCompanyModel;
use app\modules\common\models\sys\SysUserModel;
use app\modules\common\models\sys\SysVerifyAddGroupUserModel;
use app\modules\common\models\sys\SysVerifyCodeModel;
use app\response\ErrorCode;
use app\exceptions\ServiceException;


class PageCodeService extends BaseVerifyService
{
    const TYPE_RESET_PHONE = 'reset_phone';
    const TYPE_RESET_EMAIL = 'reset_email';
    const TYPE_ADD_GROUP_USER = 'add_group_user';

    /**
     * 检查页面的token状态
     * @param $token
     * @param $typeStr
     * @param bool $isVerify
     * @return array
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function getPageStatus($token, $typeStr, $isVerify = false)
    {
        if ($typeStr == self::TYPE_ADD_GROUP_USER) {
            return $this->getAddGroupUser($token);
        }


        switch ($typeStr){
            case self::TYPE_RESET_PHONE:
                $type = SysVerifyCodeModel::TYPE_RESET_PASSWORD_PHONE_PAGE;
                $res = [];
                break;
            case self::TYPE_RESET_EMAIL:
                $type = SysVerifyCodeModel::TYPE_RESET_PASSWORD_EMAIL_PAGE;
                $res = [];
                break;
            default:
                $type = 0;
                $res = [];
                break;
        }

        $time = time() - 86400;
        $query = SysVerifyCodeModel::find();
        $res = $query->asArray()
            ->select([])
            ->andWhere(['type' => $type, 'code' => $token])
            ->andWhere(['>=', 'created_time', $time])
            ->orderBy('id DESC')
            ->limit(1)
            ->one();
        if (!$res) {
            ServiceException::send(ErrorCode::USER_RESET_PASSWORD_ERROR, '页面已过期');
        }
        if ($res['is_verify'] == 1) {
            ServiceException::send(ErrorCode::USER_EMAIL_IS_OPERATED, '密码已重置');

        }
        if ($isVerify) {
            SysVerifyCodeModel::find()->createCommand()->update(SysVerifyCodeModel::tableName(),
                ['is_verify' => 1, 'updated_time' => time()], ['id' => $res['id']])->execute();
        }
        return $res;
    }

    /**
     * 管理员邀请其他人加入企业小组的时候需要输出企业名称和小组名称,邀请者名称和被邀请者名称
     * @param $token
     * @return array
     * @throws ServiceException
     */
    private function getAddGroupUser($token)
    {
        $res = [];
        $tokenInfo = SysVerifyAddGroupUserModel::find()->asArray()
            ->where(['code' => $token])
            ->orderBy('id desc')
            ->limit(1)
            ->one();
        if (!empty($tokenInfo)) {
            $companyInfo = SysCompanyModel::find()->asArray()
                ->where(['id' => $tokenInfo['company_id']])
                ->one();
            $groupInfo = SysCompanyGroupModel::find()->asArray()
                ->where(['id' => $tokenInfo['group_id'], 'company_id' => $tokenInfo['company_id']])
                ->one();

            $userIdsArr = [$tokenInfo['from_user_id'], $tokenInfo['receiver_user_id']];
            $userList = SysUserModel::find()->asArray()
                ->where("id in (" . implode(",", $userIdsArr) . ")")
                ->all();
            $userArr = !empty($userList) ? array_column($userList, 'user_name', 'id') : null;
            $fromUser = isset($userArr[$tokenInfo['from_user_id']]) ? $userArr[$tokenInfo['from_user_id']] : '';
            $receiverUser = isset($userArr[$tokenInfo['receiver_user_id']]) ? $userArr[$tokenInfo['receiver_user_id']] : '';

            if (empty($companyInfo) || empty($fromUser) || empty($receiverUser)) {
                throw new ServiceException('页面已过期', ErrorCode::USER_RESET_PASSWORD_ERROR);
            } else {
                $res = [
                    'company_name' => $companyInfo['company_name'],
                    'group_name' => $groupInfo['group_name'],
                    'from_user_name' => $fromUser,
                    'receiver_user_name' => $receiverUser,
                    //'from_user_id' => $tokenInfo['from_user_id'],
                    'receiver_user_id' => $tokenInfo['receiver_user_id'],
                    'company_id' => $tokenInfo['company_id'],
                    'group_id' => $tokenInfo['group_id'],
                ];
            }
        } else {
            throw new ServiceException('页面已过期', ErrorCode::USER_RESET_PASSWORD_ERROR);
        }
        return $res;
    }

}