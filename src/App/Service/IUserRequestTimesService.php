<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 18:00
 */

namespace App\Service;


interface IUserRequestTimesService
{
    function increaseBySessionKey(string $sessionKey): void;

    function getTimesByAccount(string $account): ?int;

    function getTimesBySessionKey(string $sessionKey): ?int;

    function getAllTodayUserRequestApiTime(): array;

    function increaseAndPushSortedSetBySessionKey(string $sessionKey): void;

    function getUserTopRequestApiTimeList(int $limit = 10): array;

    function flushAllTodayUserRequestApiTime(): void;

    function existsCacheAeSessionKey(?int $time): bool;

}