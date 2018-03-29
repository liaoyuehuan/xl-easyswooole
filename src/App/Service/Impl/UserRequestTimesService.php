<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 18:03
 */

namespace App\Service\Impl;


use App\Bean\Token;
use App\Consts\CustomConst;
use App\Model\Impl\TokenModel;
use App\Model\ITokenModel;
use App\Service\AbstractService;
use App\Service\ITokenService;
use App\Service\IUserRequestTimesService;
use Core\Component\Di;
use Core\Swoole\Async\Redis;
use Core\Swoole\Async\Redis\AbstractHandler;
use http\Env\Response;

class UserRequestTimesService extends AbstractService implements IUserRequestTimesService
{
    /**
     * @var Redis
     */
    private $redisPool;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var ITokenModel
     */
    private $tokenModel;

    /**
     * @var ITokenService
     */
    private $tokenService;

    private $prefix = 'ae_session_key';

    private $sortedSetKey = 'ae_session_key_sorted_key';

    public function __construct()
    {
        $this->redisPool = Di::getInstance()->get(CustomConst::REDIS_POOL);
        $this->redis = Di::getInstance()->get(CustomConst::REDIS);
        $this->tokenModel = TokenModel::getInstance();
        $this->tokenService = TokenService::getInstance();
    }


    function increaseBySessionKey(string $sessionKey): void
    {
        $this->redisPool->hincrby(CustomConst::CACHE_AE_SESSION_KEY,$sessionKey,1,function (){});
    }

    function getTimesByAccount(string $account): ?int
    {
        $token = $this->tokenModel->get($account);
        if (!empty($token)){
            $sessionKey = $token->getAccessToken();
            return $this->getTimesBySessionKey($sessionKey);
        } else {
            return null;
        }
    }

    function getTimesBySessionKey(string $sessionKey): int
    {
        return (int)$this->redis->hget(CustomConst::CACHE_AE_SESSION_KEY,$sessionKey);
    }

    function getTrueKey(string $sessionKey)
    {
        return CustomConst::CACHE_AE_SESSION_KEY . $sessionKey;
    }


    function getAllTodayUserRequestApiTime(): array
    {
        $tokens = $this->tokenModel->select();
        return array_map(function (Token $token){
            return [
                'account' => $token->getUserNick(),
                'times' => $this->getTimesBySessionKey($token->getAccessToken())
            ];
        },$tokens);
    }

    function increaseAndPushSortedSetBySessionKey(string $sessionKey): void
    {
        $this->redisPool->zincrby($this->sortedSetKey,1,$sessionKey,function (){});
    }

    function getUserTopRequestApiTimeList(int $limit = 10): array
    {
        $list = $this->getSessionKeyTopRequestApiTimeList($limit);
        $resultList = [];
        array_walk($list,function ($times,$sessionKey) use (&$resultList){
            $account = $this->tokenService->getAccountByAccessToken($sessionKey);
            $resultList[$account] = $times;
        });
        return $resultList;
    }

    function getSessionKeyTopRequestApiTimeList(int $limit): array {
        return $this->redis->zRevRange($this->sortedSetKey,0,$limit,true);
    }

    function flushAllTodayUserRequestApiTime(): void
    {
        $this->redis->del(CustomConst::CACHE_AE_SESSION_KEY);
    }


}