<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 11:02
 */

namespace App\Filter\Action;

use App\Http\Result;
use App\Http\ResultAckConst;
use Conf\Config;
use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;
use Core\Utility\Validate\Rule;
use Core\Utility\Validate\Validate;

class CheckAdminTokenFilter extends AbstractActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool
    {
        if (false === $this->validate($request, $response)) {
            return false;
        }
        $param = $request->getRequestParam();
        if (false === $this->checkSign($param, $param['sign'])) {
            $response->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'invalid sign'));
            return false;
        }
        return true;
    }

    private function validate(Request $request, Response $response)
    {
        $validate = new Validate();
        $validate->addField('sign')->withRule(Rule::REQUIRED);
        $validate->addField('nonce_str')->withRule(Rule::REQUIRED);
        $validate->addField('timestamp')->withRule(Rule::REQUIRED);
        $validate->addField('sign_method')->withRule(Rule::REQUIRED);
        $message = $validate->validate($request->getRequestParam());
        if ($message->hasError()) {
            $response->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        }
        return true;
    }

    private function checkSign(array $param, string $sign)
    {
        $secret = Config::getInstance()->getConf('SYSTEM_ADMIN_TOKEN');
        $sysSign = $this->sign($param, $secret);
        return strtoupper($sysSign) === strtoupper($sign);
    }

    private function sign(array $param, string $secret): string
    {
        if (isset($param['sign'])) {
        unset($param['sign']);
    }
        ksort($param);
        $signStr = '';
        foreach ($param as $k => $v) {
            $signStr .= $k . '=' . $v . '&';
        }
        $signStr .= $secret;
        unset($k, $v);
        return md5($signStr);
    }
}