<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:42
 */

namespace App\Filter\Action;

abstract class AbstractActionFilter implements IActionFilter
{
    protected static $filterInstance;

    /**
     * @return IActionFilter
     */
    public static function getInstance()
    {
        if (false === isset(static::$filterInstance[static::class])) {
            static::$filterInstance[static::class] = new static();
        }
        return static::$filterInstance[static::class];
    }
}