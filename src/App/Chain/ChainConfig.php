<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/30
 * Time: 17:25
 */

class ChainConfig
{
    private static $instance;
    /**
     * @return ChainConfig
     */
    public static function getInstance()
    {
        if (isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}