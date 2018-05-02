<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26
 * Time: 17:12
 */

namespace App\Utils;


abstract class SnowFlake
{
    const EPOCH = 1479533469598;

    const max12bit = 4095;

    const max41bit = 1099511627775;

    static $machineId = null;


    public static function machineId($mId = 0)
    {
        self::$machineId = $mId;
    }

    public static function generateSnowId(): int
    {
        //time
        $time = floor(microtime(true) * 1000);

        $time -= self::EPOCH;

        $base = decbin(self::max41bit + $time);

        if (!self::$machineId) {
            $machineid = self::$machineId;
        } else {
            $machineid = str_pad(decbin(self::$machineId), 10, '0', STR_PAD_LEFT);
        }

        $random = str_pad(decbin(mt_rand(0, self::max12bit)), 12, '0', STR_PAD_LEFT);

        $base = $base . $machineid . $random;

        return bindec($base);
    }
}