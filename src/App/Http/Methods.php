<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8/008
 * Time: 9:43
 */

namespace App\Http;


use Core\Component\Di;
use Core\Http\Request;

class Methods
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $method;


    public function __construct(string $method)
    {
        $this->init($method);
    }


    public static  function newInstance(string $method)
    {
        return new Methods($method);
    }

    public function init(string $method)
    {
        $paths = explode('.', $method);
        $this->method = array_pop($paths);
        $this->class = $this->getClass($paths);
    }


    private function getClass(array $paths)
    {
        array_unshift($paths, 'Vendor');
        array_unshift($paths, 'App');
        return implode('\\', array_map(function ($value) {
            return ucfirst($value);
        }, $paths));
    }


    public function methodExists()
    {
        if (class_exists($this->class) && method_exists($this->class, $this->method)) {
            return true;
        } else {
            return false;
        }
    }

    public function execute(array $params = [])
    {
        if ($this->methodExists()) {
            call_user_func([$this->getClassInstance(), $this->method], $params);
        } else {
            return false;
        }
    }

    private function getClassInstance()
    {
        $instance = Di::getInstance()->get(md5($this->class));
        if (empty($instance)) {
            $instance = new $this->class;
            Di::getInstance()->set(md5($this->class), $instance);
        }
        return $instance;
    }
}