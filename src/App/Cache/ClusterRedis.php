<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 11:41
 */

namespace App\Cache;

class ClusterRedis
{
    private $masters = [
        [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => ''
        ]
    ];

    private $salves = [
        [
            'host' => '127.0.0.1',
            'port' => 6380,
            'password' => ''
        ]
    ];

    private $readMethodList = [
        'get',
        'range',
        'keys',
        'val',
        'exists',
        'count',
        'card'
    ];

    private $slaveSourceCount;

    private $masterSourceCount;

    private $clusterSource = [];

    public function __construct()
    {
        $this->slaveSourceCount = count($this->salves);
        $this->masterSourceCount = count($this->masters);
    }

    public function getSource($method)
    {
        if ($this->isReadMethod($method)) {
            return $this->getReadResource();
        } else {
            return $this->getWriteResource();
        }
    }

    public function getReadResource()
    {
        $pos = mt_rand(0, $this->slaveSourceCount - 1);
        $clusterSourceKey = $this->getClusterSourceKey($pos, false);
        if (empty($this->clusterSource[$clusterSourceKey])) {
            $host = $this->salves[$pos]['host'];
            $port = $this->salves[$pos]['port'];
            $password = $this->salves[$pos]['password'] ?? null;
            $this->clusterSource[$clusterSourceKey] = $this->createSource($host, $port, $password);
        }
        return $this->clusterSource[$clusterSourceKey];
    }

    public function getWriteResource()
    {
        $pos = mt_rand(0, $this->masterSourceCount - 1);
        $clusterSourceKey = $this->getClusterSourceKey($pos, true);
        if (empty($this->clusterSource[$clusterSourceKey])) {
            $host = $this->masters[$pos]['host'];
            $port = $this->masters[$pos]['port'];
            $password = $this->masters[$pos]['password'] ?? null;
            $this->clusterSource[$clusterSourceKey] = $this->createSource($host, $port, $password);
        }
        return $this->clusterSource[$clusterSourceKey];
    }

    public function createSource($host, $port = 6379, $password = null)
    {
        $redis = new \Redis();
        if ($redis->pconnect($host, $port)) {
            if (!empty($password)) {
                if ($redis->auth($password)) {
                    return $redis;
                } else {
                    return null;
                }
            } else {
                return $redis;
            }
        } else {
            return null;
        }
    }

    public function getClusterSourceKey($key, $isMaster = true)
    {
        if ($isMaster === true) {
            return 'master-' . $key;
        } else {
            return 'slave-' . $key;
        }
    }

    private function isReadMethod($method)
    {
        $checkMethod = strtolower($method);
        foreach ($this->readMethodList as $readMethods) {
            if (strpos($checkMethod, $readMethods) !== false) {
                return true;
            }
        }
        return false;
    }

    public function __call($name, $arguments)
    {
        $source = $this->getSource($name);
        return call_user_func_array([$source, $name], $arguments);
    }
}