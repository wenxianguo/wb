<?php

namespace app\modules\common\lib;


class VerifyCode
{

    /**
     * 生成随机数
     * @param $length
     * @return string
     */
    public static function randomKeys($length)
    {
        $rs = '';
        $letter = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );
        for ($n = 0; $n < $length - 1; $n++) {
            $rs .= $letter[mt_rand(0, 9)];
        }
        $rs .= $letter[mt_rand(1, 9)];
        return $rs;
    }

    /**
     * 生成随机数，0-9，a-z，A-Z
     * @param $length
     * @return string
     */
    public static function randomStr($length)
    {
        $rs = '';
        $a=range('a','z');
        $b=range('A','Z');
        $c=range('0','9');
        $letter=array_merge($a,$b,$c);
        $count = count($letter) - 1;
        for ($n = 0; $n < $length; $n++) {
            $rs .= $letter[mt_rand(0, $count)];
        }
        return $rs;
    }

}
