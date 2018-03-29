<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 14:18
 */

namespace Core\Swoole\Async\Redis;


class SelectHandler extends AbstractHandler implements Handler
{
    function join(\swoole_redis $redis, $result)
    {
        var_dump(22);

        $redis->get('test1', function ($redis, $result) {
            var_dump($result);
            if ($result) {
                if ($this->getHandler() != null) {
                    $this->getHandler()->join($redis, $result);
                } else {
                    ($this->joinFunc)($redis, $result);
                }
            }

        });
    }
}
