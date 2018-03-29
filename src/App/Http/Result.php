<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6/006
 * Time: 11:56
 */

namespace App\Http;

class Result implements \JsonSerializable
{
    /**
     * @var string
     */
    public $Ack;

    /**
     * @var  Error[]
     */
    public $Errors ;

    /**
     * @var
     */
    public $data;

    /**
     * Result constructor.
     * @param string|null $Ack
     * @param null $data
     * @param Error[] $errors
     */
    public function __construct(string $Ack = null,$data = null, array $errors = null)
    {
        $this->Ack = $Ack;
        $this->data = $data;
        $this->Errors = $errors;
    }

    /**
     * @param Error $error
     */
    public function addError(Error $error)
    {
        $Errors[] = $error;
    }

    /**
     * @return Error[]|array
     */
    public function getErrors()
    {
        return $this->Errors;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        $result = array_filter($vars, function ($value) {
            if ($value === null )
                return false;
            else
                return true;
        });
        return $result;
    }
}