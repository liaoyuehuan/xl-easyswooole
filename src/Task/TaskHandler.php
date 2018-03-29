<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/24
 * Time: 16:37
 */

namespace Task;


class TaskHandler
{
    public static function execTask($taskName): void
    {
        $class = 'Task\\' . $taskName . 'Task';
        if (class_exists($class)) {
            $task = new $class();
            $task->run();
        } else {
            echo 'task-not-found' . PHP_EOL;
        }
    }

   
}