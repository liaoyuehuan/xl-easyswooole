<?php

namespace Core\Swoole\Async;

use Core\Swoole\Async\Redis\ConnectHandler;
use Core\Swoole\Async\Redis\PasswordHandler;
use Core\Swoole\Async\Redis\SelectHandler;
use Swoole;

class Redis extends Pool
{
    const DEFAULT_PORT = 6379;

    function __construct($config = array(), $poolSize = 100)
    {
        if (empty($config['host'])) {
            throw new \Core\Swoole\Exception\InvalidParam("require redis host option.");
        }
        if (empty($config['port'])) {
            $config = self::DEFAULT_PORT;
        }
        parent::__construct($config, $poolSize);
        $this->create(array($this, 'connect'));
    }

    protected function connect()
    {
        $config = $this->config;
        unset($config['host']);
        unset($config['port']);
        $redis = new \swoole_redis($config);
        $joinFun = function (\swoole_redis $redis, $result) {
            if ($result) {
                $this->join($redis);
            } else {
                $this->failure();
                trigger_error("connect to redis server[{$this->config['host']}:{$this->config['port']}] failed. Error: {$redis->errMsg}[{$redis->errCode}].");
            }
        };
        $connectHandler = new ConnectHandler($joinFun, $this->config);
//        $passwordHandler = new PasswordHandler($joinFun, $this->config);
//        $selectHandler = new SelectHandler($joinFun,$this->config);
//        if (isset($this->config['password'])) {
//            $connectHandler->setHandler($passwordHandler);
//            if (isset($this->config['database'])){
//                $passwordHandler->setHandler($selectHandler);
//            }
//        } else {
//            if (isset($this->config['database'])){
//                $connectHandler->setHandler($selectHandler);
//            }
//        }
        $redis->on('close', function ($redis) {
            $this->remove($redis);
        });
        $connectHandler->join($redis, '');;
//        return $redis->connect($this->config['host'], $this->config['port'], function ($redis, $result) {
//            if (isset($this->config['password'])) $$$redis->auth($this->config['password']);
//            if ($result) {
//                $this->join($redis);
//            } else {
//                $this->failure();
//                trigger_error("connect to redis server[{$this->config['host']}:{$this->config['port']}] failed. Error: {$redis->errMsg}[{$redis->errCode}].");
//            }
//        });
    }

    function __call($call, $params)
    {
        return $this->request(function (\swoole_redis $redis) use ($call, $params) {
            call_user_func_array(array($redis, $call), $params);
            //必须要释放资源，否则无法被其他重复利用
            $this->release($redis);
        });
    }
}