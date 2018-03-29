<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29/029
 * Time: 16:53
 */

namespace App\Controller\Api\AliExpress;


use App\Controller\Api\AbstractBase;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Vendor\Aliexpress\AliexpressPub;
use App\Vendor\Aliexpress\AliexpressRuntimeException;
use Core\Http\Message\Status;

class Qimen extends AbstractBase
{
    function rest()
    {
        try {
            $response = AliexpressPub::getInstance()->execute($this->request()->getRequestParam());
            $this->response()->writeJsonWithNoCode(
                Status::CODE_OK,
                 $response);
        } catch (AliexpressRuntimeException $e) {
            $this->response()
                ->writeJsonWithNoCode(
                    Status::CODE_INTERNAL_SERVER_ERROR,
                    new Result(ResultAckConst::FAIL,
                        null,
                        [new Error('Param Error!',
                            $e->getMessage()
                        )]
                    )
                );
            $this->response()->end();
        }
    }

    function index()
    {
    }

    function onRequest($actionName)
    {
    }

    function afterAction()
    {
    }


}