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
use App\Model\Impl\CompanyModel;
use App\Model\Impl\TokenModel;
use App\Model\ITokenModel;
use App\Service\AbstractService;
use App\Service\ITokenService;
use App\Vendor\Aliexpress\AliexpressRuntimeException;
use Core\Component\Di;
use Core\Swoole\Async\Redis;
use function PHPSTORM_META\elementType;

class TokenService extends AbstractService implements ITokenService
{

    /**
     * @var ITokenModel
     */
    private $tokenModel;

    /**
     * @var CompanyModel
     */
    private $companyModel;

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
        $this->companyModel = CompanyModel::getInstance();
        $this->redis = Di::getInstance()->get(CustomConst::REDIS);
        $this->redisPool = Di::getInstance()->get(CustomConst::REDIS_POOL);
    }


    /**
     * @param $id
     * @return Token
     */
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
        $data['expire_time'] = $data['expire_time'] / 1000;
        $data['refresh_token_valid_time'] = $data['refresh_token_valid_time'] / 1000;
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

    function pagination($param = [])
    {
        $limitParam = $this->getLimitParamFromParam($param);
        return $this->tokenModel->pagination($limitParam->page, $limitParam->limit, function (\MysqliDb $db) use ($param) {
            if (!empty($param['user_nick'])) $db->where('user_nick', $param['user_nick']);
        });
    }

    function getAccountByAccessToken(string $accessToken): ?string
    {
        $token = $this->getTokenByAccessToken($accessToken);
        if ($token) {
            return $token->getUserNick();
        } else {
            return null;
        }
    }


    /**
     * @param string $accessToken
     * @return Token|null
     */
    function getTokenByAccessToken(string $accessToken): ?Token
    {
        $data = $this->redis->get($this->getCacheAccessTokenTrueKey($accessToken));
        if (empty($data)) {
            $data = $this->tokenModel->getOne(function (\MysqliDb $db) use ($accessToken) {
                $db->where('access_token', $accessToken);
            });
            if (!empty($data)) {
                $this->redisPool->set(
                    $this->getCacheAccessTokenTrueKey($accessToken),
                    json_encode($data),
                    'EX',
                    CustomConst::CACHE_EXPIRE_SECONDS,
                    function () {
                    });
            } else {
                return null;
            }
        } else {
            $data = new Token(json_decode($data, true));
        }
        return $data;
    }

    private function getCacheAccessTokenTrueKey(string $accessToken)
    {
        return CustomConst::CACHE_ACCESS_TOKEN_TO_ACCOUNT . '-' . $accessToken;
    }

    function updateStatus(string $id, int $status): bool
    {
        $token = $this->tokenModel->get($id);
        if (!$token) {
            throw  new \RuntimeException('account not found:update error');
        }
        $token->setStatus($status);
        $token->setMTime(time());
        return $this->tokenModel->update($id, $token);
    }

    function updateTimes(string $id, int $times): bool
    {
        $token = $this->tokenModel->get($id);
        if (!$token) {
            throw  new \RuntimeException('account not found:update error');
        }
        $token->setLimitApiTimes($times);
        $token->setMTime(time());
        $this->redisPool->del($this->getCacheAccessTokenTrueKey($token->getAccessToken()), function () {
        });
        return $this->tokenModel->update($id, $token);
    }

    function updateCompany(string $id, int $company_id): bool
    {
        $token = $this->tokenModel->get($id);
        if (!$token) {
            throw  new \RuntimeException('account not found:update error');
        }
        $company = $this->companyModel->get($company_id);
        if (!$company) {
            throw  new \RuntimeException('company not found:update error');
        }
        $token->setCompanyId($company_id);
        $token->setMTime(time());
        return $this->tokenModel->update($id, $token);
    }


}