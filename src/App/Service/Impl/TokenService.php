<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 15:43
 */

namespace App\Service\Impl;

use App\Bean\Token;
use App\Consts\CustomConst;
use App\Model\Impl\TokenModel;
use App\Model\ITokenModel;
use App\Service\AbstractService;
use App\Service\ITokenService;
use App\Vendor\Aliexpress\AliexpressRuntimeException;
use Core\Component\Di;
use Core\Http\Response;
use Core\Swoole\Async\Redis;

class TokenService extends AbstractService implements ITokenService
{

    /**
     * @var ITokenModel
     */
    private $tokenModel;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var Redis
     */
    private $redisPool;

    public function __construct()
    {
        $this->tokenModel = TokenModel::getInstance();
        $this->redis = Di::getInstance()->get(CustomConst::REDIS);
        $this->redisPool = Di::getInstance()->get(CustomConst::REDIS_POOL);
    }


    function get($id)
    {
        if (empty($id)) {
            throw new AliexpressRuntimeException('user_nick must be required');
        }
        return $this->tokenModel->get($id);
    }

    function update($data): bool
    {

    }

    function insert($data): bool
    {

    }

    function merge($data): bool
    {
        $data['expire_time'] =  $data['expire_time'] / 1000;
        $data['refresh_token_valid_time'] =  $data['refresh_token_valid_time'] / 1000;
        $token = $this->tokenModel->createSplBeanFromData($data);
        return $this->tokenModel->merge($token);
    }


    function insertGetInsertId($bean): string
    {
    }

    function select($param = []): array
    {
        return $this->tokenModel->select();
    }

    function pagination($param = []){
        $limitParam  = $this->getLimitParamFromParam($param);
        return $this->tokenModel->pagination($limitParam->page,$limitParam->limit,function (\MysqliDb $db) use($param){
            if(!empty($param['user_nick'])) $db->where('user_nick',$param['user_nick']);
        });
    }

    function getAccountByAccessToken(string $accessToken): ?string
    {
        $data = $this->redis->get($this->getCacheAccessTokenTrueKey($accessToken));
        if(empty($data) ){
            $account = $this->tokenModel->getOne(function (\MysqliDb $db) use ($accessToken){
               $db->where('access_token',$accessToken);
            });
            if (!empty($account)) {
                $data = $account->getUserNick();
                $this->redisPool->set(
                $this->getCacheAccessTokenTrueKey($accessToken),
                $data,
                'EX',
                CustomConst::CACHE_EXPIRE_SECONDS,
                function (){});
            }
        }
        return $data;
    }

    private function getCacheAccessTokenTrueKey(string $accessToken){
        return CustomConst::CACHE_ACCESS_TOKEN_TO_ACCOUNT.'-'.$accessToken;
    }


}