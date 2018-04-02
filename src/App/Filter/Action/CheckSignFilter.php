<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:33
 */

namespace App\Filter\Action;

use Conf\Config;
use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;

class CheckSignFilter extends AbstractActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool
    {
        if (false === \SpiUtils::checkSign4FileRequest(
                $request->getRequestParam(),
                Config::getInstance()->getConf('ALI_EXPRESS.SECRET_KEY')
            )
        ) {
            $result = [
                'sub_message' => 'Illegal request',
                'flag' => 'failure',
                'sub_code' => 'sign-check-failure'
            ];
            $response
                ->writeJsonWithNoCode(
                    Status::CODE_OK,
                    $result
                );
            return false;
        };
        return true;
    }
}