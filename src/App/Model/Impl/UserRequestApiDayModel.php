<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 11:55
 */

namespace App\Model\Impl;


use App\Bean\UserRequestApiDay;
use App\Model\IUserRequestApiDayModel;

class UserRequestApiDayModel extends AbstractBaseModel implements IUserRequestApiDayModel
{
    public function getSqlBean()
    {
        return UserRequestApiDay::class;
    }

}