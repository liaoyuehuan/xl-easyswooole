<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/10/010
 * Time: 17:17
 */

namespace App\Controller\Api\AliExpress;


use App\Controller\Api\AbstractBase;
use App\Http\Error;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\TokenService;
use App\Vendor\Aliexpress\AliexpressPub;
use App\Vendor\Aliexpress\AliexpressRuntimeException;
use App\Vendor\Db\DbFactory;
use Core\Http\Message\Status;
use Core\Http\Response;
use Swoole\Mysql\Exception;

class Authorize extends AbstractBase
{
    public function index()
    {
        $this->response()->redirect(AliexpressPub::getInstance()->buildAuthorizeUrl());
    }

    public function getAccessToken()
    {
        $code = $this->request()->getRequestParam('code');
        $response = AliexpressPub::getInstance()->getAccessTokenInfo($code);
        if (isset($response['error_code'])) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $response)
            );
        } else if (TokenService::getInstance()->merge($response)) {
            $refresh_expire_time = date('Y-m-d H:i:s',$response['refresh_token_valid_time'] / 1000);
            $access_expire_time = date('Y-m-d H:i:s',$response['expire_time'] / 1000);
            $html = "
                        <table style='margin: 200px auto;'>
                            <tr><td>Account: </td><td>{$response['user_nick']}</td></tr>
                            <tr><td>Refresh_token: </td><td>{$response['refresh_token']}</td></tr>
                            <tr><td>Refresh_expire_time: </td><td>$refresh_expire_time</td></tr>
                            <tr><td>Access_token: </td><td>{$response['access_token']}</td></tr>
                            <tr><td>Access_expire_time: </td><td>$access_expire_time</td></tr>
                        </table>
                    ";
            $this->response()->write($html);
        } else {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, null, [
                    new Error('Save error', 'Token message save error')
                ])
            );
        };
    }

    function getAccessTokenInfo()
    {
        $id = $this->request()->getRequestParam('user_nick');
        try {
            $tokenBean = TokenService::getInstance()->get($id);
            if ($tokenBean) {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::SUCCESS, $tokenBean)
                );
            } else {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, 'token info not be found')
                );
            }

        } catch (AliexpressRuntimeException $e) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, null, [
                    new Error('Params Incomplete', $e->getMessage())
                ])
            );
        }

    }

    function onRequest($actionName)
    {
    }

    function afterAction()
    {
    }


}