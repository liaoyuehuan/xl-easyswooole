<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6/006
 * Time: 15:11
 */

namespace App\Controller\Api\AliExpress;

use App\Controller\Api\AbstractBase;
use App\Http\Result;
use App\Http\ResultAckConst;
use App\Service\Impl\CompanyService;
use App\Vendor\Aliexpress\AliexpressPub;
use App\Vendor\Aliexpress\Client;
use App\Vendor\Db\DbFactory;
use App\Vendor\Db\MysqliDb;
use Core\Component\Di;
use Core\Http\Message\Status;
use Core\Utility\Validate\Message;
use Core\Utility\Validate\Rule;
use Core\Utility\Validate\Validate;
use Swoole\Mysql;

class Test extends AbstractBase
{


    function index()
    {
        $this->response()->write('AliExpress test api');
    }

    function db()
    {
//        $this->response()->write(new MysqliDb());
//        return;
        $data = json_decode('{"access_token":"50002500e25kpCxnqE86K2lqyZvqe8hgP3orU1ce7a5b6jHexgXEqTAGYzB6vTYZ1mn","refresh_token":"50003500625qFzrLeVgpow4vyZc0hVinPZvoD157250badDbT3MStDgHvXIvB2tG89p","w1_valid":1547198660714,"refresh_token_valid_time":1515662659690,"w2_valid":1515664459690,"user_id":"2268181875","expire_time":1547198660714,"r2_valid":1515921859690,"locale":"zh_CN","r1_valid":1547198660714,"sp":"ae","user_nick":"cn1512040081"}'
            , true);
        $this->response()->write((Di::getInstance()->get('db')->insert(
            'token', $data
        )));
    }

    function select()
    {
        $data = DbFactory::getDbConnect()->get('token');
        $this->response()->write($data);
    }

    function selectw()
    {
        $server = array(
            'host' => 'rm-vy1645607ukkh5dw4.mysql.rds.aliyuncs.com',
            'user' => 'xiaoliao',
            'password' => 'god#2018',
            'database' => 'my_db',
        );
        $db = new Mysql();

        $db->connect($server, function (Mysql $db, $result) {
            $this->response()->write($db);
            $db->query('select * from obj_token', function (Mysql $db, $result) {
                ob_start();
                if ($result === false) {
                    var_dump($db->error, $db->errno);
                } elseif ($result === true) {
                    var_dump($db->affected_rows, $db->insert_id);
                } else {
                    var_dump($result);
                    $db->close();
                }
                $data = ob_get_contents();
                ob_clean();
                $this->response()->write($data);

            });
        });
    }

    public function message()
    {
        $session_key = '50000101230pVyazgwCLxByuDXGAvgPWvEfhti4HSw1623c962IDXAqpB17Kgpk9x7p';
        $request = new \AliexpressMessageRedefiningQuerymsgrelationlistRequest();
        $res = Client::getInstance()->execute($request, $session_key);
        $this->response()->writeJsonWithNoCode(Status::CODE_OK, $res);
    }

    public function getCompany()
    {
        $data = CompanyService::getInstance()->pagination();
        $this->response()->writeJsonWithNoCode(Status::CODE_OK, $data);
    }



    function afterAction()
    {

    }

    function onRequest($actionName)
    {

    }

}