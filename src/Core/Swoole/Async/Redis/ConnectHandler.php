<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 14:45
 */

namespace Core\Swoole\Async\Redis;

class ConnectHandler extends AbstractHandler implements Handler
{
    function join(\swoole_redis $redis, $result)
    {
        $redis->connect($this->config['host'], $this->config['port'], function (\swoole_redis $redis, $result) {
            if ($result) {
                if ($this->getHandler() != null) {
                    var_dump('sadfiksadnfsadbnkfusadbnuifv');
                    $this->getHandler()->join($redis, $result);
                } else {
                    ($this->joinFunc)($redis, $result);
                }
            }
        });
    }

}