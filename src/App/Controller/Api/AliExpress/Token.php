<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17/017
 * Time: 14:09
 */

namespace App\Controller\Api\AliExpress;


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

    public function getAllAccountRequestTimes(){
        $list = UserRequestTimesService::getInstance()->getAllTodayUserRequestApiTime();
        $this->response()->writeJsonWithNoCode(Status::CODE_OK,
            new Result(ResultAckConst::SUCCESS, $list));
    }

    public function getTenTopAccountRequestTime(){
        $list = UserRequestTimesService::getInstance()->getUserTopRequestApiTimeList(10);
        $this->response()->writeJsonWithNoCode(Status::CODE_OK,
            new Result(ResultAckConst::SUCCESS, $list));
    }

    function onRequest($actionName)
    {

    }

    function afterAction()
    {
    }

}