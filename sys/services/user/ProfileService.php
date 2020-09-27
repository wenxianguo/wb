<?php

namespace app\modules\sys\services\user;

use app\exceptions\ServiceException;
use app\modules\common\models\sys\SysUserModel;
use app\modules\common\services\BaseService;
use app\modules\sys\services\verify\BindCodeService;
use app\response\ErrorCode;
use app\modules\common\services\OptLogService;

class ProfileService extends BaseService
{
    /** @var UserService $userService */
    private $userService;
    public function __construct()
    {
        $this->userService = $this->getService(UserService::class);
    }

    /**
     * 获取用户信息
     *
     * @param $userId
     * @return array
     */
    public function getProfileInfo($userId)
    {
        $userInfo = $this->userService->findById($userId);
        return $userInfo;
    }

    /**
     * 修改用户资料
     *
     * @param $userId
     * @param array $data
     * @return bool
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function updateProfile($userId, array $data)
    {
        $updateData = [];
        foreach ($data as $key => $value) {
            !is_null($value) && $updateData[$key] = $value;
        }
        if (empty($updateData)) {
            return true;
        }

        // 查找用户
        $userInfo = $this->userService->findById($userId);
        if (empty($userInfo)) {
            throw new ServiceException('找不到用户', ErrorCode::USER_NOT_EXIST);
        }


        // 更新数据并写入操作日志
        $updateData['updated_time'] = time();

        $userInfo = $this->userService->findById($userId);
        $this->userService->update($userInfo, $updateData);

        $this->updateOperateLog($userId, $updateData, $userInfo);

        return $userInfo;
    }


    /**
     * 用户修改密码
     * @param $userId
     * @param array $data
     * @return mixed
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function updatePassword($userId, string $newPassword)
    {
        // 查找用户
        $userInfo = $this->userService->findById($userId);
        if (empty($userInfo)) {
            throw new ServiceException('找不到用户', ErrorCode::USER_NOT_EXIST);
        }

        $updateData = [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'updated_time' => time(),
        ];
        $this->userService->update($userInfo, $updateData);

        return $userInfo;
    }

    /**
     * 绑定邮箱
     *
     * @param $userId
     * @param $email
     * @param $code
     * @return mixed
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function bindEmail($userId, $email, $code)
    {
        $res = BindCodeService::getInstance()->verifyBindEmail($userId, $email, $code);
        if (!$res) {
            throw new ServiceException('系统错误');
        }
        $data = ['email' => $email, 'updated_time' => time()];
        $userInfo = $this->userService->findById($userId);
        $this->userService->update($userInfo, $data);
        $userInfo->email = $email;

        $this->updateOperateLog($userId, $data, $userInfo);

        return $userInfo;
    }

    /**
     * 绑定手机号
     *
     * @param $userId
     * @param $phone
     * @param $code
     * @return mixed
     * @throws ServiceException
     * @throws \yii\db\Exception
     */
    public function bindPhone($userId, $phone, $code)
    {
        $res = BindCodeService::getInstance()->verifyBindPhone($userId, $phone, $code);
        if (!$res) {
            throw new ServiceException('系统错误');
        }
        $userInfo = $this->userService->findById($userId);
        $data = ['phone' => $phone, 'updated_time' => time()];
        $this->userService->update($userInfo, $data);
        $userInfo->phone = $phone;

        $this->updateOperateLog($userId, $data, $userInfo);

        return $userInfo;
    }

    private function updateOperateLog(int $id, array $params, $info)
    {
        list($oldContent, $newContent) = $this->getOperateInfo($params, $info);
        OptLogService::getInstance()->setCond(SysUserModel::class, 'updated', ['id' => $id])
            ->setUpdateOldValue($oldContent)
            ->setUpdateNewValue($newContent)
            ->log();
    }
}