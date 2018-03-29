<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/1/23
 * Time: 上午12:06
 */

namespace Conf;


use App\Cache\ClusterRedis;
use App\Consts\CustomConst;
use App\RPC\DemoRpc;
use App\RPC\UserRpc;
use Core\AbstractInterface\AbstractEvent;
use Core\AutoLoader;
use Core\Component\Di;
use Core\Component\Version\Control;
use Core\Http\Request;
use Core\Http\Response;
use Core\Swoole\Async\Redis;
use Core\Swoole\Server;

class Event extends AbstractEvent
{
    function frameInitialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        AutoLoader::getInstance()->requireFile('App/Vendor/Taobao/TopSdk.php');
        AutoLoader::getInstance()->requireFile('App/Vendor/Db/MysqliDb.php');
        Di::getInstance()->set('db', \MysqliDb::class, Array(
            Config::getInstance()->getConf('CONF')
        ));
        Di::getInstance()->set(CustomConst::REDIS_POOL, Redis::class, Array(
            Config::getInstance()->getConf('REDIS'), 5
        ));
//        Di::getInstance()->set(CustomConst::REDIS, function () {
//            $redis = new \Redis();
//            $redis->pconnect(
//                '127.0.0.1',
//                6380
//            );
//            return $redis;
//        });
        Di::getInstance()->set(CustomConst::REDIS, ClusterRedis::class);
    }

    function frameInitialized()
    {
        // TODO: Implement frameInitialized() method.
    }


    function beforeWorkerStart(\swoole_server $server)
    {

        $config = new \Core\Component\RPC\Common\Config();
        $server = new \Core\Component\RPC\Server\Server($config);
        $server->registerServer('demo')->setActionRegisterClass(DemoRpc::class);
        $server->registerServer('user')->setActionRegisterClass(UserRpc::class);
        $server->attach(9502);
    }

    function onStart(\swoole_server $server)
    {
        // TODO: Implement onStart() method.
    }

    function onShutdown(\swoole_server $server)
    {
        // TODO: Implement onShutdown() method.
    }

    function onWorkerStart(\swoole_server $server, $workerId)
    {

        if ($workerId == 0) {
            //地柜扫描目录
            $file_scan_func = function ($dir) use (&$file_scan_func) {
                $data[] = $dir;
                if (is_dir($dir)) {
                    $fileArray = array_diff(scandir($dir), ['.', '..']);
                    array_walk($fileArray, function ($file) use ($dir, &$data, &$file_scan_func) {
                        $file_path = $dir . '/' . $file;
                        $data = array_merge($data, $file_scan_func($file_path));
                    });
                } else {
                    $data[] = $dir;
                }
                return $data;
            };
            $san_dir = ROOT . '/App';
            $file_list = $file_scan_func($san_dir);
            //为所有目录添加inotify监视
            $notify_instance = inotify_init();
            array_walk($file_list, function ($file) use ($notify_instance) {
                inotify_add_watch($notify_instance, $file, IN_CREATE | IN_DELETE | IN_MODIFY);
            });

            //加入EventLoop
            swoole_event_add($notify_instance, function () use ($notify_instance) {
                $events = inotify_read($notify_instance);
                if (!empty($events)) {
                    //注意更新多个文件的间隔时间处理,防止一次更新了10个文件，重启了10次，懒得做了，反正原理在这里
                    Server::getInstance()->getServer()->reload();
                }
            });
        }

    }

    function onWorkerStop(\swoole_server $server, $workerId)
    {
        // TODO: Implement onWorkerStop() method.
    }

    function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.
    }

    function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        // TODO: Implement onDispatcher() method.
    }

    function onResponse(Request $request, Response $response)
    {
        // TODO: Implement afterResponse() method.
    }

    function onTask(\swoole_server $server, $taskId, $workerId, $taskObj)
    {
        // TODO: Implement onTask() method.
    }

    function onFinish(\swoole_server $server, $taskId, $taskObj)
    {
        // TODO: Implement onFinish() method.
    }

    function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_code)
    {
        // TODO: Implement onWorkerError() method.
    }
}
