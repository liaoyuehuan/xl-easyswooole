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
use App\Utils\CacheUtils;
use Core\Component\Di;
use Core\Swoole\Async\Redis;

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
        $this->redisPool->hincrby(
            CacheUtils::getDateKey(CustomConst::CACHE_AE_SESSION_KEY), $sessionKey, 1, function () {
        });
    }

    function getTimesByAccount(string $account): ?int
    {
        $token = $this->tokenModel->get($account);
        if (!empty($token)) {
            $sessionKey = $token->getAccessToken();
            return $this->getTimesBySessionKey($sessionKey);
        } else {
            return null;
        }
    }

    function getTimesBySessionKey(string $sessionKey, ?int $time = null): int
    {
        return (int)$this->redis->hget(
            CacheUtils::getDateKey(CustomConst::CACHE_AE_SESSION_KEY, $time), $sessionKey
        );
    }


    function getAllTodayUserRequestApiTime(?int $time = null): array
    {
        $tokens = $this->tokenModel->select();
        return array_map(function (Token $token) use ($time) {
            return [
                'account' => $token->getUserNick(),
                'times' => $this->getTimesBySessionKey($token->getAccessToken(), $time)
            ];
        }, $tokens);
    }

    function increaseAndPushSortedSetBySessionKey(string $sessionKey, ?int $time = null): void
    {
        $this->redisPool->zincrby(
            CacheUtils::getDateKey($this->sortedSetKey, $time), 1, $sessionKey, function () {
        });
    }

    function getUserTopRequestApiTimeList(int $limit = 10): array
    {
        $list = $this->getSessionKeyTopRequestApiTimeList($limit);
        $resultList = [];
        array_walk($list, function ($times, $sessionKey) use (&$resultList) {
            $account = $this->tokenService->getAccountByAccessToken($sessionKey);
            $resultList[$account] = $times;
        });
        return $resultList;
    }

    function getSessionKeyTopRequestApiTimeList(int $limit): array
    {
        return $this->redis->zRevRange(CacheUtils::getDateKey($this->sortedSetKey), 0, $limit, true);
    }

    function flushAllTodayUserRequestApiTime(?int $time = null): void
    {
        $this->redis->del(CacheUtils::getDateKey(CustomConst::CACHE_AE_SESSION_KEY, $time));
        $this->redis->del(CacheUtils::getDateKey($this->sortedSetKey, $time));
    }

    function existsCacheAeSessionKey(?int $time): bool
    {
        return $this->redis->exists(CacheUtils::getDateKey(CustomConst::CACHE_AE_SESSION_KEY, $time));
    }

}