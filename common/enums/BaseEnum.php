<?php

namespace app\modules\common\enums;

class BaseEnum
{
    const MESSAGE = [];

    public static function getMessage(int $code)
    {
        $message = '';
        if (isset(static::MESSAGE[$code])) {
            $message = static::MESSAGE[$code];
        }
        return $message;
    }

    public static function getKeyByVal(string $val)
    {
        foreach (static::MESSAGE as $key => $value) {
            if ($val == $value) {
                break;
            }
        }
        return $key;
    }
}
