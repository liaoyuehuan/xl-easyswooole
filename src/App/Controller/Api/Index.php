<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/15
 * Time: 下午8:21
 */

namespace App\Controller\Api;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use Core\Http\Message\UploadFile;
use Swoole\Http\Request;

class Index extends AbstractController
{

    function index()
    {
        // TODO: Implement index() method.
        //$this->response()->write("this is api index");/*  url:domain/api/index.html  domain/api/  */
    }

    function phpinfo(){
        ob_start();
        phpinfo();
        $content = ob_get_contents();
        ob_end_clean();
        $this->response()->write($content);
    }


    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }

    function onRequest($actionName)
    {
        $this->request()->getServerParams();
//        $this->response()->write($actionName);
        // TODO: Implement onRequest() method.
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFount() method.
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterResponse()
    {
        // TODO: Implement afterResponse() method.
    }
    function test(){
        $file = $this->request()->getUploadedFile("a");
        if($file instanceof UploadFile){
            $file->moveTo(ROOT."/Temp/a.json");
        }
    }
}