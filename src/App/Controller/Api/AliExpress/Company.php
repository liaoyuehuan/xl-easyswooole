<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/27
 * Time: 10:59
 */

namespace App\Controller\Api\AliExpress;


use App\Controller\Api\AbstractBase;
use App\Filter\Action\AbstractActionFilter;
use App\Filter\Action\CheckSystemApiSignFilter;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\CompanyService;
use App\Service\Impl\TokenService;
use Core\Http\Message\Status;
use Core\Utility\Validate\Rule;
use Core\Utility\Validate\Validate;

class Company extends AbstractBase
{

    /**
     * @var AbstractActionFilter[]
     */
    private $filterList = [
        CheckSystemApiSignFilter::class
    ];

    function index()
    {

    }


    function updateTokenCompany()
    {
        $param = $this->request()->getRequestParam();
        $validate = new Validate();
        $validate->addField('account')->withRule(Rule::REQUIRED);
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        }
        try {
            $result = TokenService::getInstance()->updateCompany($param['account'], 19);
            if ($result) {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::SUCCESS, 'update company success'));
            } else {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, 'system error: update company error'));
            }
        } catch (\RuntimeException $re) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $re->getMessage()));
        }
    }

    function queryTokens()
    {
        $company = $this->getCompany();
        $data = CompanyService::getInstance()->queryTokens($company->getId(), $this->request()->getRequestParam());
        $this->response()->writeJsonWithNoCode(
            Status::CODE_OK,
            new Result(ResultAckConst::SUCCESS, $data));
    }

    function queryAccountRequestTimes()
    {
        $param = $this->request()->getRequestParam();
        $validate = new Validate();
        $validate->addField('account')->withRule(Rule::REQUIRED);
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        }
        $company = $company = $this->getCompany();
        $param['company_id'] = $company->getId();
        $data = CompanyService::getInstance()->queryUserRequestApi($param);
        if ($data) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::SUCCESS, $data));
        } else {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'account not found'));
        }
    }

    function getAccessToken()
    {
        $param = $this->request()->getRequestParam();
        $validate = new Validate();
        $validate->addField('account')->withRule(Rule::REQUIRED);
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        }
        try {
            $company = $company = $this->getCompany();
            $token = TokenService::getInstance()->get($param['account']);

            if ($token && $token->getCompanyId() === $company->getId()) {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::SUCCESS, [
                        'account' => $token->getUserNick(),
                        'expire_time' => date('Y-m-d H:i:s',$token->getExpireTime()),
                        'access_token' => $token->getAccessToken()
                    ])
                );
            } else {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, 'account not fund'));
            }

        } catch (\RuntimeException $re) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $re->getMessage()));
        }
    }

    function resetAppSecret()
    {
        $app_secret = CompanyService::getInstance()->resetAppSecret($this->getCompany()->getId());
        if ($app_secret) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::SUCCESS, $app_secret)
            );
        } else {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'system error'));
        }
    }

    function onRequest($actionName)
    {
        foreach ($this->filterList as $actionFilterClass) {
            $actionFilter = $actionFilterClass::getInstance();
            $canContinue = $actionFilter->requestHandler($this->request(), $this->response());
            if ($canContinue === false) {
                $this->response()->end();
                return false;
            }
        }
    }

    /**
     * @return \App\Bean\Company|null
     */
    private function getCompany()
    {
        $app_id = $this->request()->getRequestParam('app_id');
        $company = CompanyService::getInstance()->getByAppId($app_id);
        return $company;
    }


    function afterAction()
    {
    }

}