<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 20:52
 */

namespace App\RPC;


use Core\Component\RPC\AbstractInterface\AbstractActionRegister;
use Core\Component\RPC\Common\ActionList;
use Core\Component\RPC\Common\Package;
use Core\Component\Socket\Client\TcpClient;

class UserRpc extends AbstractActionRegister
{
    function register(ActionList $actionList)
    {
        $actionList->registerAction('who', function (Package $package, Package $res, TcpClient $client) {
            var_dump('your package is :' . $package);
            $res->setMessage('this is user.who');
       });
        $actionList->registerAction('login', function (Package $package, Package $res, TcpClient $client) {
            var_dump('your package is :' . $package);
            $res->setMessage('this is user.login');
       });
        $actionList->registerAction('who', function (Package $package, Package $res, TcpClient $client) {
            var_dump('your package is :' . $package);
            $res->setMessage('this is user.default ');
       });

    }

}