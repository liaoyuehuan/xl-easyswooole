<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/24
 * Time: 16:42
 */

namespace Task;


class DemoTask implements ITask
{
    function run(): void
    {
        echo 'hello';
    }
}