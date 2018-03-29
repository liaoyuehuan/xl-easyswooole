<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 12:01
 */

namespace App\Model\Impl;


use App\Bean\Token;
use App\Model\IBaseModel;

class TokenModel extends AbstractBaseModel implements IBaseModel
{
    protected $pk = 'user_nick';

    protected $expectUpdatePro = [
        'c_time'
    ];

    public function getSqlBean()
    {
        return Token::class;
    }

}