<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6/006
 * Time: 17:55
 */

namespace App\Controller\Api\AliExpress;


use App\Controller\Api\AbstractBase;
use App\Filter\Action\CheckLimitRequestTimes;
use App\Filter\Action\CheckSignFilter;
use App\Filter\Action\CheckTokenFilter;
use App\Filter\Action\StatisticsRequestTimeFilter;
use App\Http\Error;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Vendor\Aliexpress\AliexpressPub;
use App\Vendor\Aliexpress\Exceptions\AliexpressMehtodNotOpen;
use App\Vendor\Aliexpress\Exceptions\AliexpressRuntimeException;
use Conf\Config;
use Core\Http\Message\Status;

class Route extends AbstractBase
{

    private $filterList = [
        CheckSignFilter::class,
        CheckLimitRequestTimes::class,
        StatisticsRequestTimeFilter::class,
        CheckTokenFilter::class
    ];

    function index()
    {
        $this->response()->write('welcome AliExpress api');
    }


    function afterAction()
    {

    }

    public function onRequest($actionName)
    {
        $param = $this->getParam();
        foreach ($this->filterList as $actionFilterClass) {
            $actionFilter = $actionFilterClass::getInstance();
            $canContinue = $actionFilter->requestHandler($this->request(), $this->response(), $param);
            if ($canContinue === false) {
                $this->response()->end();
                return false;
            }
        }
    }

    function rest()
    {
        try {
            $param = $this->getParam();
            $response = AliexpressPub::getInstance()->execute($param);
            $this->hasParam() && $response = json_encode($response);
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