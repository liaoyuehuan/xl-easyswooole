<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:33
 */

namespace App\Filter\Action;

use Core\Http\Request;
use Core\Http\Response;

interface IActionFilter
{
    function requestHandler(Request $request, Response $response, $params = []): bool;
}