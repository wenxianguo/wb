<?php

namespace app\modules\common\models\sys;

use app\modules\common\models\BaseModel;
use yii\base\Event;

class SysUserModel extends BaseModel
{

    public static function tableName()
    {
        return 'sys_user';
    }

    public function create(array $data)
    {
        $sysUser = new static();
        $sysUser = $this->arrayToProperties($sysUser, $data);
        $sysUser->save();
        return $sysUser;
    }

    public function updateSysUser(self $sysUser, array $data)
    {
        $sysUser = $this->arrayToProperties($sysUser, $data);
        $sysUser->save();
        return $sysUser;
    }

    public function findByEmail(string $email)
    {
        return self::find()
            ->where('email = :email')
            ->andWhere('is_del = :isDel')
            ->params(['email' => $email, 'isDel' => 0])
            ->one();
    }

    public function findByPhone(int $phone)
    {
        return self::find()
            ->where('phone = :phone')
            ->andWhere('is_del = :isDel')
            ->params(['phone' => $phone, 'isDel' => 0])
            ->one();
    }

    public function findByUsername(string $username)
    {
        return self::find()
            ->where('user_name = :username')
            ->andWhere('is_del = :isDel')
            ->params(['username' => $username, 'isDel' => 0])
            ->one();
    }

    public function likeByUsername(string $username, array $field = [])
    {
        return self::find()
            ->select($field)
            ->where(['like', 'user_name', $username])
            ->asArray()
            ->all();
    }

    public function updateCallback(Event $event)
    {
        $id = (int)$event->sender->id;
        $this->findById($id);
        $this->deleteCache();
    }

    public function findBySearchList(array $searchList, int $offset = 0, $limit = 20)
    {
        return self::find()
            ->where($searchList)
            ->offset($offset)
            ->limit($limit)
            ->orderBy('id desc')
            ->all();
    }

    public function totalBySearchList(array $searchList) :int
    {
        $total = self::find()
            ->where($searchList)
            ->count();
        return (int)$total;
    }
}
