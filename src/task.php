<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/1/22
 * Time: 下午9:46
 */
require_once 'Core/Core.php';
\Core\Core::getInstance()->frameWorkInitialize();

if(!isset($argv[1])){
    exit( 'missing argument 1: task-name'.PHP_EOL);
}
$taskName = $argv[1];

\Task\TaskHandler::execTask($taskName);

