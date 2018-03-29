<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 14:14
 */
namespace Core\Swoole\Async\Redis;

interface Handler
{
    function join(\swoole_redis $redis,$result);
}