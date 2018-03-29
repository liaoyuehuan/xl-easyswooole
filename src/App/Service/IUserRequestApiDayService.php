<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 12:00
 */

namespace App\Service;


interface IUserRequestApiDayService
{
    function syncRedisRequestDataToDb(): void;
}