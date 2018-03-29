<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 14:16
 */

namespace Core\Swoole\Async\Redis;


abstract class AbstractHandler implements Handler
{
    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Closure
     */
    protected $joinFunc;

    public function __construct(callable &$joinFunc,array $config = [])
    {
        $this->joinFunc = $joinFunc;
        $this->config = $config;
    }

    /**
     * @return Handler
     */
    public function getHandler(): ?Handler
    {
        return $this->handler;
    }

    /**
     * @param Handler $handler
     */
    public function setHandler(?Handler &$handler): void
    {
        $this->handler = $handler;
    }


}