<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 20:47
 */
namespace App\RPC;
use Core\Component\RPC\AbstractInterface\AbstractActionRegister;
use Core\Component\RPC\Common\ActionList;
use Core\Component\RPC\Common\Package;

class DemoRpc extends AbstractActionRegister
{
    function register(ActionList $actionList)
    {
        $actionList->setDefaultAction(function (Package $req,Package $res){
            $res->setMessage('this is a rpc demo');
        });
    }

}