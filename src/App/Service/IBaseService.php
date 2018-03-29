<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 15:34
 */

namespace App\Service;

interface IBaseService
{

    function get($id): string;

    function update($data): bool;

    function insert($data): bool;

    function insertGetInsertId($bean): string;

    function select($param): array;
}