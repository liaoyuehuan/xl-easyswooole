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

class SyncRedisToDbTask implements  ITask
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
        $this->userRequestApiDayService->syncRedisRequestDataToDb();
    }

}