<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/24
 * Time: 16:09
 */

namespace App\Service\Impl;


use App\Bean\UserRequestApiDay;
use App\Consts\CustomConst;
use App\Model\Impl\UserRequestApiDayModel;
use App\Model\IUserRequestApiDayModel;
use App\Service\AbstractService;
use App\Service\ITokenService;
use App\Service\IUserRequestApiDayService;
use Core\Component\Di;
use Core\Swoole\Async\Redis;

class UserRequestApiDayService extends AbstractService implements IUserRequestApiDayService
{


    /**
     * @var UserRequestTimesService
     */
    private $userRequestTimesService;

    /**
     * @var IUserRequestApiDayModel
     */
    private $userRequestApiDayModel;

    /**
     * @var Redis
     */
    private $redisPool;

    public function __construct()
    {
        $this->userRequestTimesService = UserRequestTimesService::getInstance();
        $this->userRequestApiDayModel = UserRequestApiDayModel::getInstance();
        $this->redisPool = Di::getInstance()->get(CustomConst::REDIS_POOL);
    }

    function syncRedisRequestDataToDb(): void
    {
        $redisList = $this->userRequestTimesService->getAllTodayUserRequestApiTime();
        $userRequestApiDayList = array_map(function ($value) {
            $userRequestApiDay = new UserRequestApiDay();
            $userRequestApiDay->setAccount($value['account']);
            $userRequestApiDay->setTimes($value['times']);
            return $userRequestApiDay;
        }, $redisList);
        $this->userRequestTimesService->flushAllTodayUserRequestApiTime();
        $this->userRequestApiDayModel->insertMulti($userRequestApiDayList);
    }

}