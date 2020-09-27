<?php

namespace app\modules\common\lib;


class Account
{
    public static function formatGoogle($accountId)
    {
        $accountArr = str_split($accountId, 3);
        $accountStr = '';
        foreach ($accountArr as $key => $value) {
            if (in_array($key, [1,2])) {
                $accountStr .= '-' . $value;
            } else {
                $accountStr .= $value;
            }
        }
        return $accountStr;
    }
}
