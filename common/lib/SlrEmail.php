<?php

namespace app\modules\common\lib;

use Mobvista\MixTools\Src\Request\RequestApi;
use Yii;

class SlrEmail
{
    /**
     * 发送邮件
     * @param string $toEmail 接收者
     * @param string $title 标题
     * @param string $content 内容
     * @param int $type 类型
     * @param string $sendUser 发送方
     * @param array $options 额外参数
     * @throws \Exception
     */
    public static function sendMail($toEmail = 'Nobody', $title = 'From Adpope', $content = 'empty', $type = 0, $sendUser = 'adpope', $options = [])
    {
        $url = TASK_API . Yii::$app->params['fb_sdk_api']['send_email'];
        $params = [
            'email' => $toEmail,
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'options' => $options,
            'send_user' => $sendUser,
        ];
        RequestApi::get($url, $params);
    }
}
