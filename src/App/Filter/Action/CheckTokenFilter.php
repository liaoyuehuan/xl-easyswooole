<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:54
 */

namespace App\Filter\Action;


use App\Http\Error;
use App\Http\Result;
use App\Http\ResultAckConst;
use Conf\Config;
use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;

class CheckTokenFilter extends AbstractActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool
    {
        if (!$this->checkToken($params)) {
            $response
                ->writeJsonWithNoCode(
                    Status::CODE_FORBIDDEN,
                    new Result(ResultAckConst::FAIL,
                        null,
                        [new Error('Invalid Token!!',
                            'This is an Invalid Token. Please enter a Valid Token!'
                        )]
                    )
                );
            return false;
        }
        return true;
    }

    private function checkToken($param)
    {
        return $param['token'] === $this->getTokenPub($param['method']);
    }

    private function getTokenPub($route)
    {
        return md5( $route. '-api-' . Config::getInstance()->getConf('token_priv'));
    }


}