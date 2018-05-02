<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/27
 * Time: 9:43
 */

namespace App\Auth;

use App\Utils\SnowFlake;
use Conf\Config;

class SystemApiAuth
{
    public static function encrypt(string $password): string
    {
        return password_hash(
            md5($password) . Config::getInstance()->getConf('SYSTEM_AUTH_KEY') ?? '',
            PASSWORD_DEFAULT
        );
    }

    public static function generateAppId(): int
    {
        return SnowFlake::generateSnowId();
    }

    public static function generateAppSecret(): string
    {
        $rand = microtime(true);
        return strtoupper(sha1(md5($rand)));
    }
}