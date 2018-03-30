<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 10:09
 */

namespace App\Utils;

class CacheUtils
{
    public static function getDateKey(string  $key,?int  $time = null)
    {
        $time === null && $time = time();
        return date('Ymd-',$time) . $key;
    }

}