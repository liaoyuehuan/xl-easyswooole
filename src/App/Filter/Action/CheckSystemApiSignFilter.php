<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/27
 * Time: 11:43
 */

namespace App\Filter\Action;


use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\CompanyService;
use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;
use Core\Utility\Validate\Rule;
use Core\Utility\Validate\Validate;

class CheckSystemApiSignFilter extends AbstractActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool
    {
        if (false === $this->validate($request, $response)) {
            return false;
        }
        $company = CompanyService::getInstance()->getByAppId((int)$request->getRequestParam('app_id'));
        if (empty($company)) {
            $response->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'this is app_ip not found'));
            return false;
        }
        if (false === $this->checkSign($request->getRequestParam(), $company->getAppSecret())) {
            $response->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'sign invalid'));
            return false;
        }
        return true;
    }

    function validate(Request $request, Response $response): bool
    {
        $validate = new Validate();
        $validate->addField('app_id')->withRule(Rule::REQUIRED);
        $validate->addField('sign')->withRule(Rule::REQUIRED);
        $validate->addField('sign_method')->withRule(Rule::REQUIRED);
        $message = $validate->validate($request->getRequestParam());
        if ($message->hasError()) {
            $response->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $param
     * @param string $appSecret
     * @return bool
     */
    function checkSign($param, string $appSecret)
    {
        $app_id = $param['app_id'];
        $sign = $param['sign'];
        $sign_method = $param['sign_method'];
        if ($sign_method === 'md5') {
            $systemSign = md5($app_id . $sign_method . $appSecret);
        } else {
            $systemSign = md5($app_id . $sign_method . $appSecret); //默认使用MD5
        }
        return strtoupper($sign) === strtoupper($systemSign);
    }


}