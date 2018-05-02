<?php

/**
 * Created by PhpStorm.
 * User: YF
 * Date: 16/8/25
 * Time: 上午12:05
 */

namespace Conf;

use Core\Component\Spl\SplArray;

class Config
{
    private static $instance;
    protected $conf;

    function __construct()
    {
        $conf = $this->sysConf() + $this->userConf();
        $this->conf = new SplArray($conf);
    }

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function getConf($keyPath)
    {
        return $this->conf->get($keyPath);
    }

    /*
            * 在server启动以后，无法动态的去添加，修改配置信息（进程数据独立）
    */
    function setConf($keyPath, $data)
    {
        $this->conf->set($keyPath, $data);
    }

    private function sysConf()
    {
        return array(
            "SERVER" => array(
                "LISTEN" => "0.0.0.0",
                "SERVER_NAME" => "",
                "PORT" => 443,
                "RUN_MODE" => SWOOLE_PROCESS,//不建议更改此项
                "SERVER_TYPE" => \Core\Swoole\Config::SERVER_TYPE_WEB,//
                'SOCKET_TYPE' => SWOOLE_TCP,//当SERVER_TYPE为SERVER_TYPE_SERVER模式时有效
                "CONFIG" => array(
                    'task_worker_num' => 8, //异步任务进程
                    "task_max_request" => 10,
                    'daemonize' => false,
                    'log_file' => 'swoole.log',
                    'max_request' => 5000,//强烈建议设置此配置项
                    'worker_num' => 8,
                    'reactor_num' => 4,
                    'dispatch_mode' => 1,
                    'max_connection' => 65535,
                    'open_cpu_affinity' => true,
                    'tcp_fastopen' => true,
//                    'user' => 'php',
//                    'group' => 'php'
                ),
            ),
            "DEBUG" => array(
                "LOG" => true,
                "DISPLAY_ERROR" => true,
                "ENABLE" => true,
            ),
            "CONTROLLER_POOL" => true//web或web socket模式有效
        );
    }

    private function userConf()
    {
        return array(
            'token_priv' => '479fe803d70b9a45100c976628810b27c908fdde',
            'ALI_EXPRESS' => [
                'APP_KEY' => '24764211',
                'SECRET_KEY' => '1104b7c4c4bcc88e44e8736984559f30'
            ],
            'CONF' => [
                'host' => 'rm-vy1g9mdi024h5g0c7.mysql.rds.aliyuncs.com',
                'username' => 'xiaoliao',
                'password' => 'god#2018',
                'db' => 'my_db',
                'port' => 3306,
                'prefix' => 'obj_',
                'charset' => 'utf8'
            ],
            'REDIS' => [
                'host' => '127.0.0.1',
                'port' => 6379
            ],
            'SYSTEM_AUTH_KEY' => '$cf#cZcv',
            'SYSTEM_ADMIN_TOKEN' => 'OcpIwmcmASciAdnOndlioLAd'
        );
    }
}