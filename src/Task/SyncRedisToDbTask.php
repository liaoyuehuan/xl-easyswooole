<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/24
 * Time: 16:25
 */

namespace Task;

use App\Service\Impl\UserRequestApiDayService;
use App\Service\IUserRequestApiDayService;
use Swoole\Mysql\Exception;

class SyncRedisToDbTask implements ITask
{

    /**
     * @var IUserRequestApiDayService
     */
    private $userRequestApiDayService;

    public function __construct()
    {
        $this->userRequestApiDayService = UserRequestApiDayService::getInstance();
    }

    function run(): void
    {
        try {
            $this->userRequestApiDayService->syncRedisRequestDataToDb();
            echo 'success';
        } catch (\RuntimeException $re) {
            echo $re->getFile() . ' - ' . $re->getLine() . ':' . $re->getMessage();
        } catch (\Exception $e) {
            $e->getTraceAsString();
        }
        echo PHP_EOL;
    }

}