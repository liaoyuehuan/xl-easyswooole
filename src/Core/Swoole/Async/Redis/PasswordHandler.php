<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 14:40
 */

namespace Core\Swoole\Async\Redis;


class PasswordHandler extends AbstractHandler implements Handler
{
    function join(\swoole_redis $redis, $result)
    {
        $redis->auth($this->config['password'], function (\swoole_redis $redis, $result) {
            var_dump($result);
            if ($result) {
                if ($this->getHandler() != null) {
                    $this->getHandler()->join($redis, $result);
                } else {
                    ($this->joinFunc)($redis, $result);
                }
            }
        });
        $redis->errCode;
    }
}