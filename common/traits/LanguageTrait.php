<?php
namespace app\modules\common\traits;

trait LanguageTrait
{
    /**
     * 获取语言
     *
     * @return void
     */
    public function getLanguage() : string
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $acceptLanguage = explode(',', $acceptLanguage);
        $language = $acceptLanguage[0];
        return $language;
    }
}