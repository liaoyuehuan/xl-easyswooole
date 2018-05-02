<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/27
 * Time: 13:41
 */

namespace App\Controller\Api\AliExpress;


use App\Controller\Api\AbstractBase;
use App\Filter\Action\AbstractActionFilter;
use App\Filter\Action\CheckAdminTokenFilter;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\CompanyService;
use Core\Component\Spl\SplBean;
use Core\Http\Message\Status;
use Core\Utility\Validate\Rule;
use Core\Utility\Validate\Validate;


class Auth extends AbstractBase
{
    /**
     * @var AbstractActionFilter[]
     */
    private $filterList = [
        CheckAdminTokenFilter::class
    ];

    function index()
    {
        // TODO: Implement index() method.
    }


    public function registerCompany()
    {
        $param = $this->request()->getRequestParam();
        $validate = new Validate();
        $validate->addField('name')->withRule(Rule::REQUIRED);
        $validate->addField('password')
            ->withRule(Rule::REQUIRED)
            ->withRule(Rule::MIN_LEN, 6)
            ->withRule(Rule::MAX_LEN, 16);
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        }
        if (CompanyService::getInstance()->getByName($param['name'])) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'company has registered,can not register'));
            return false;
        };
        $result = CompanyService::getInstance()->insertGetInsertId($param);
        if ($result) {
            $company = CompanyService::getInstance()->get($result);
            $company->setPassword(null); //清除密码
            $data = $company->toArray(SplBean::FILTER_TYPE_NOT_EMPTY);
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::SUCCESS, $data));
            return true;
        } else {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, 'system error'));
            return false;
        }
    }

    public function getCompany()
    {
        $param = $this->request()->getRequestParam();
        $validate = new Validate();
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
            return false;
        }
        try {
            $result = CompanyService::getInstance()->queryCompany($param);
            if ($result) {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::SUCCESS, $result));
            } else {
                $this->response()->writeJsonWithNoCode(
                    Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, 'company not found'));
            }
        } catch (\RuntimeException $re) {
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $re->getMessage()));
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

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }

}