<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6/006
 * Time: 13:52
 */

namespace App\Controller\Api;

use App\Http\Error;
use App\Http\Result;
use App\Http\ResultAckConst;
use Conf\Config;
use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;

abstract  class AbstractBase extends AbstractController
{



    public function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()
            ->writeJsonWithNoCode(
                Status::CODE_NOT_FOUND,
                new Result(ResultAckConst::FAIL,
                    [new Error('Router not found!',
                        'Router not found,Please enter a Valid Router!'
                    )]
                )
            );
        $this->response()->end();
    }
}