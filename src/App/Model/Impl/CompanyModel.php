<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26
 * Time: 11:07
 */

namespace App\Model\Impl;


use App\Bean\Company;
use App\Model\ICompanyModel;

class CompanyModel extends AbstractBaseModel implements ICompanyModel
{
    public function getSqlBean()
    {
        return Company::class;;
    }

}