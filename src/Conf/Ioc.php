<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27
 * Time: 9:39
 */

namespace Conf;


use App\Consts\CustomConst;
use App\Model\Impl\UserRequestApiDayModel;
use App\Model\IUserRequestApiDayModel;
use App\Service\Impl\TokenService;
use App\Service\Impl\UserRequestApiDayService;
use App\Service\ITokenService;
use App\Service\IUserRequestApiDayService;
use Core\AutoLoader;
use Core\Component\Di;
use Core\Swoole\Async\Redis;

class Ioc
{
    public static function handler()
    {
        $di = Di::getInstance();
        AutoLoader::getInstance()->requireFile('App/Vendor/Db/MysqliDb.php');
        $di->set('db', \MysqliDb::class, Array(
            Config::getInstance()->getConf('CONF')
        ));

        $di->set(CustomConst::REDIS_POOL, Redis::class, Array(
            Config::getInstance()->getConf('REDIS'), 10
        ));

        $redis = new \Redis();
        $redis->pconnect(
            Config::getInstance()->getConf('REDIS.host'),
            Config::getInstance()->getConf('REDIS.port')
        );
        if ($redis->ping()) {
            $di->set(CustomConst::REDIS, $redis);
        }

        $di->set(IUserRequestApiDayService::class,UserRequestApiDayService::class);

        $di->set(IUserRequestApiDayModel::class,UserRequestApiDayModel::class);

        $di->set(ITokenService::class,TokenService::class);
    }
}