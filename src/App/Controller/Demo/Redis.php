<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/21
 * Time: 17:18
 */

namespace App\Controller\Demo;

use App\Consts\CustomConst;
use App\Controller\Api\AbstractBase;
use App\Service\Impl\UserRequestTimesService;
use Core\Component\Di;

class Redis extends AbstractBase
{
    function index()
    {
        $this->response()->write('redis demo ');
    }

    public function test()
    {
       # complete redis pool demo
//        $config = [
//            'host' => '127.0.0.1',
//            'port' => 6379,
////            'database' => 1,
//        ];
//        $redis = Di::getInstance()->get('redis-pool');
//        $ok = $redis->incr('test3',function ($redis,$result){
//            var_dump($result);
//        });

        #redis_pool demo
//        $redis_pool = Di::getInstance()->get(CustomConst::REDIS_POOL);
//             $redis_pool->incr('test6',function ($redis,$result){
//        });

        #redi demo
        $redis = Di::getInstance()->get(CustomConst::REDIS);
        $count = $redis->get('test6');
        $this->response()->write('test4: '.$count);
    }

    function onRequest($actionName)
    {

    }

    function afterAction()
    {

    }

}