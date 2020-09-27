<?php

namespace app\modules\common\listeners;

class BaseListeners
{
    public function getFooter()
    {
        $content = '<br /><br />如果您有任何疑问，可以回复这封邮件向我们提问。';
        $content .= '<br /><br />丝路团队';
        return $content;
    }

    public function getEnv()
    {
        $env = '';
        if (!in_array(ENVIRONMENT, ['pre_release', 'production'])) {
            $env = ENVIRONMENT;
        }
        return $env;
    }
}
