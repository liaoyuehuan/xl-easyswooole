<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6/006
 * Time: 17:55
 */

namespace App\Controller\Api\AliExpress;


use App\Consts\CustomConst;
use App\Controller\Api\AbstractBase;
use App\Http\Error;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\UserRequestTimesService;
use App\Vendor\Aliexpress\AliexpressPub;
use App\Vendor\Aliexpress\Exceptions\AliexpressMehtodNotOpen;
use App\Vendor\Aliexpress\Exceptions\AliexpressRuntimeException;
use Conf\Config;
use Core\Http\Message\Status;

class Route extends AbstractBase
{

    function index()
    {
        $this->response()->write('welcome AliExpress api');
    }


    function afterAction()
    {

    }

    public function onRequest($actionName)
    {
        if (false === \SpiUtils::checkSign4FileRequest(
                $this->request()->getRequestParam(),
                Config::getInstance()->getConf('ALI_EXPRESS.SECRET_KEY')
            )
        ){
            $result = [
                'sub_message' => 'Illegal request',
                'flag' => 'failure',
                'sub_code' => 'sign-check-failure'
            ];
            $this->response()
                ->writeJsonWithNoCode(
                    Status::CODE_OK,
                    $result
                );
            $this->response()->end();
        };
        $param = $this->getParam();
        $times = UserRequestTimesService::getInstance()->getTimesBySessionKey($param['session_key']);
        if ($times > CustomConst::MAX_USER_REQUEST_EVERY_DAY) {
            $result = [
                'sub_message' => 'request-limit',
                'flag' => 'failure',
                'sub_code' => 'you has requested the max times :' . CustomConst::MAX_USER_REQUEST_EVERY_DAY
            ];
            $this->response()
                ->writeJsonWithNoCode(
                    Status::CODE_OK,
                    $result
                );
            $this->response()->end();
            return false;
        }
        UserRequestTimesService::getInstance()->increaseBySessionKey($param['session_key']);
        UserRequestTimesService::getInstance()->increaseAndPushSortedSetBySessionKey($param['session_key']);

        if (!$this->checkToken()) {
            $this->response()
                ->writeJsonWithNoCode(
                    Status::CODE_FORBIDDEN,
                    new Result(ResultAckConst::FAIL,
                        null,
                        [new Error('Invalid Token!!',
                            'This is an Invalid Token. Please enter a Valid Token!'
                        )]
                    )
                );
            $this->response()->end();
            return false;
        }

    }

    function rest()
    {
        try {
            $param = $this->getParam();
            file_put_contents('/var/www/easyswoole/App/Controller/Api/AliExpress/param.txt', json_encode($param));
            $response = AliexpressPub::getInstance()->execute($param);
            $this->hasParam() && $response = json_encode($response);
//            file_put_contents('/var/www/easyswoole/App/Controller/Api/AliExpress/data.txt', $response);
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::SUCCESS, $response
                ));
        } catch (AliexpressRuntimeException $e) {
            $this->response()
                ->writeJsonWithNoCode(
                    Status::CODE_INTERNAL_SERVER_ERROR,
                    new Result(ResultAckConst::FAIL,
                        null,
                        [new Error('Param Error!',
                            $e->getMessage()
                        )]
                    )
                );
            $this->response()->end();
        } catch (AliexpressMehtodNotOpen $e) {
            $this->response()
                ->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::FAIL,
                        null,
                        [new Error('Method Error!',
                            'Method Not open for the time being'
                        )]
                    )
                );
            $this->response()->end();
        }
    }

    public function getParam($key = null)
    {
        $param = $this->request()->getRequestParam();
        $this->hasParam() && $param = json_decode($param['param'], true);
        if (!$key) {
            return $param;
        } else {
            return isset($param[$key]) ? $param[$key] : null;
        }
    }

    /**
     * @return bool
     */
    private function hasParam()
    {
        $param = $this->request()->getRequestParam();
        return isset($param['param']);
    }

    private function getMethod()
    {
        return $this->getParam('method');
    }

    private function getRoute()
    {
        return $this->getMethod();
    }

    private function checkToken()
    {
        return $this->getParam('token') === $this->getTokenPub();
    }

    private function getTokenPub()
    {
        return md5($this->getRoute() . '-api-' . Config::getInstance()->getConf('token_priv'));
    }


}