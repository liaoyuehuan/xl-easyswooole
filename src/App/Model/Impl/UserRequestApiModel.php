<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 11:53
 */

namespace App\Model\Impl;


use App\Bean\UserRequestApi;
use App\Model\IUserRequestApiModel;

class UserRequestApiModel extends AbstractBaseModel implements IUserRequestApiModel
{
    public function getSqlBean()
    {
        return UserRequestApi::class;
    }

}