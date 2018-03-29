<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 15:44
 */

namespace App\Service;


use App\Bean\Token;

interface ITokenService
{
    /**
     * @param $id
     * @return Token
     */
    function get($id);

    /**
     * @param $data
     * @return bool
     */
    function update($data): bool;

    function insert($data): bool;

    function insertGetInsertId($bean): string;

    /**
     * @param $param
     * @return Token[]
     */
    function select($param): array;

    function getAccountByAccessToken(string $accessToken): ?string;
}