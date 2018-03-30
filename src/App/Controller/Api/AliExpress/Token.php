<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17/017
 * Time: 14:09
 */

namespace App\Controller\Api\AliExpress;


use App\Consts\TokenStatusConst;
use App\Controller\Api\AbstractBase;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\TokenService;
use App\Service\Impl\UserRequestTimesService;
use Core\Http\Message\Status;
use Core\Utility\Validate\Rule;
use Core\Utility\Validate\Validate;

class Token extends AbstractBase
{

    function index()
    {
    }

    public function query()
    {
        $data = TokenService::getInstance()->pagination();
        $this->response()->writeJsonWithNoCode(Status::CODE_OK,
            new Result(ResultAckConst::SUCCESS, $data));
    }

    public function getAccountRequestTimes()
    {
        $validate = new Validate();
        $param = $this->request()->getRequestParam();
        $validate->addField('account')->withRule(Rule::REQUIRED)->withMsg('account must be required');
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
        } else {
            $times = UserRequestTimesService::getInstance()->getTimesByAccount($param['account']);
            if ($times !== null) {
                $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                    new Result(ResultAckConst::SUCCESS, $times));
            } else {
                $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, 'this account not exists'));
            }
        }
    }

    public function getAllAccountRequestTimes()
    {
        $list = UserRequestTimesService::getInstance()->getAllTodayUserRequestApiTime();
        $this->response()->writeJsonWithNoCode(Status::CODE_OK,
            new Result(ResultAckConst::SUCCESS, $list));
    }

    public function getTenTopAccountRequestTime()
    {
        $list = UserRequestTimesService::getInstance()->getUserTopRequestApiTimeList(10);
        $this->response()->writeJsonWithNoCode(Status::CODE_OK,
            new Result(ResultAckConst::SUCCESS, $list));
    }


    public function updateTokenStatus()
    {
        $validate = new Validate();
        $param = $this->request()->getRequestParam();
        $validate->addField('account')->withRule(Rule::REQUIRED)->withMsg('account must be required');
        $validate->addField('status')->withRule(Rule::REQUIRED)->withMsg('status must be required');
        $validate->addField('status')->withRule(Rule::IN,
            TokenStatusConst::OPEN,
            TokenStatusConst::CLOSED
        )->withMsg('status value must be 0 or 1');
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
        } else {
            $account = $param['account'];
            $status = $param['status'];
            try {
                if (TokenService::getInstance()->updateStatus($account, $status)) {
                    $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                        new Result(ResultAckConst::SUCCESS, 'update token status success'));
                } else {
                    $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                        new Result(ResultAckConst::FAIL, 'update token status error'));
                };
            } catch (\RuntimeException $re) {
                $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, $re->getMessage()));
            }
        }
    }

    public function updateTokenTimes()
    {
        $validate = new Validate();
        $param = $this->request()->getRequestParam();
        $validate->addField('account')->withRule(Rule::REQUIRED)->withMsg('account must be required');
        $validate->addField('times')->withRule(Rule::REQUIRED)->withMsg('times must be required');
        $validate->addField('times')->withRule(Rule::MIN,1
        )->withMsg('times must be larger 1');
        $message = $validate->validate($param);
        if ($message->hasError()) {
            $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                new Result(ResultAckConst::FAIL, $message->all()));
        } else {
            $account = $param['account'];
            $times = $param['times'];
            try {
                if (TokenService::getInstance()->updateTimes($account, $times)) {
                    $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                        new Result(ResultAckConst::SUCCESS, 'update token times success'));
                } else {
                    $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                        new Result(ResultAckConst::FAIL, 'update token times error'));
                };
            } catch (\RuntimeException $re) {
                $this->response()->writeJsonWithNoCode(Status::CODE_OK,
                    new Result(ResultAckConst::FAIL, $re->getMessage()));
            }
        }
    }

    function onRequest($actionName)
    {

    }

    function afterAction()
    {
    }

}