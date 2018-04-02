<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:49
 */

namespace App\Filter\Action;

use App\Consts\CustomConst;
use App\Model\Impl\UserRequestApiDayModel;
use App\Service\Impl\TokenService;
use App\Service\Impl\UserRequestTimesService;
use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;

class CheckLimitRequestTimes extends AbstractActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool
    {
        $token = TokenService::getInstance()->getTokenByAccessToken($params['session_key']);
        if (empty($token)) {
            $result = [
                'sub_message' => 'token not found ',
                'flag' => 'failure',
                'sub_code' => 'please authorization in the aliexpress service  market !'
            ];
            $response
                ->writeJsonWithNoCode(
                    Status::CODE_OK,
                    $result
                );
            return false;
        }
        $times = UserRequestTimesService::getInstance()->getTimesBySessionKey($params['session_key']);
        if ($times > $token->getLimitApiTimes()) {
            $result = [
                'sub_message' => 'request-limit',
                'flag' => 'failure',
                'sub_code' => 'you has requested the max times :' . $token->getLimitApiTimes() . ' !please contact administrator'
            ];
            $response
                ->writeJsonWithNoCode(
                    Status::CODE_OK,
                    $result
                );
            return false;
        }
        return true;
    }

}