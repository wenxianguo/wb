<?php

namespace app\modules\common\lib;

use Mobvista\MixTools\Src\Elk\Elk;
use Mobvista\MixTools\Src\Request\RequestApi;

class ShortLink
{
    /**
     * 创建短链
     * @param string $url
     * @return
     * @throws \Exception
     */
    public static function create(string $url)
    {
        $url = SHORT_LINK . $url;
        $data = RequestApi::get($url);
        if (!isset($data['ae_url'])) {
            $data['link'] = $url;
            Elk::log(ElkLogType::CREATE_SHORT_LINK_ERROR, var_export($data, true), Elk::LEVEL_ERROR);
        }
        return $data['ae_url'];
    }
}
