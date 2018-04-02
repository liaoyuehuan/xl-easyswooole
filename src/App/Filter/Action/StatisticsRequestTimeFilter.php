<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:51
 */

namespace App\Filter\Action;


use App\Service\Impl\UserRequestTimesService;
use Core\Http\Request;
use Core\Http\Response;

class StatisticsRequestTimeFilter extends AbstractActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool
    {
        UserRequestTimesService::getInstance()->increaseBySessionKey($params['session_key']);
        UserRequestTimesService::getInstance()->increaseAndPushSortedSetBySessionKey($params['session_key']);
        return true;
    }
}