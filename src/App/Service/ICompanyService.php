<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26
 * Time: 11:13
 */

namespace App\Service;


use App\Bean\Company;

interface ICompanyService extends IBaseService
{
    /**
     * @param $param
     * @return Company[]
     */
    function select($param = []): array;

    function update($data): bool;

    function add($data): bool;

    function selectUserRequestApi($app_id, $param = []): array;

    function pagination($param = []);

    function getByName(string $name): ?Company;

    function getByAppId(int $appId): ?Company;

    function queryTokens(int $company_id, $param = []);

    function queryUserRequestApi($param = []);

    function getByAdminId(int $admin_id): ?Company;

    function queryCompany($param = []);

    function resetAppSecret(int $id): ?string ;

}