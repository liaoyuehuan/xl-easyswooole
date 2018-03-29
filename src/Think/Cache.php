<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think;

use Conf\Config;
use Think\Cache\Driver;

/**
 * @see \Think\Cache\Driver
 */
class Cache
{

    private static $selfInstance;

    /**
     * 缓存实例
     * @var array
     */
    protected $instance = [];

    /**
     * 应用对象
     * @var Config
     */
    protected $config;

    /**
     * 操作句柄
     * @var object
     */
    protected $handler;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    /**
     * @see \Think\Cache\Driver
     */
    public static function getInstance(){
        if(self::$selfInstance === null){
            self::$selfInstance = new self();
        }
        return self::$selfInstance;
    }

    /**
     * 连接缓存
     * @access public
     * @param  array         $options  配置数组
     * @param  bool|string   $name 缓存连接标识 true 强制重新连接
     * @return Driver
     */
    public function connect(array $options = [], $name = false)
    {
        $type = !empty($options['type']) ? $options['type'] : 'File';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset($this->instance[$name])) {
            $class = false !== strpos($type, '\\') ? $type : '\\think\\cache\\driver\\' . ucwords($type);


            if (true === $name) {
                $name = md5(serialize($options));
            }

            $this->instance[$name] = new $class($options);
        }

        return $this->instance[$name];
    }

    /**
     * 自动初始化缓存
     * @access public
     * @param  array         $options  配置数组
     * @return Driver
     */
    public function init(array $options = [])
    {
        if (is_null($this->handler)) {
            // 自动初始化缓存
            $config = $this->config;

            if (empty($options) && 'complex' == $config->get('cache.type')) {
                $default = $config->get('cache.default');
                $options = $config->get('cache.' . $default['type']) ?: $default;
            } elseif (empty($options)) {
                $options = $config->pull('cache');
            }

            $this->handler = $this->connect($options);
        }

        return $this->handler;
    }

    /**
     * 切换缓存类型 需要配置 cache.type 为 complex
     * @access public
     * @param  string $name 缓存标识
     * @return Driver
     */
    public function store($name = '')
    {
        if ('' !== $name && 'complex' == $this->config->getConf('cache.type')) {
            return $this->connect($this->config->getConf('cache.' . $name), strtolower($name));
        }

        return $this->init();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->init(), $method], $args);
    }

}
