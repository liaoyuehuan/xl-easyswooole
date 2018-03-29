<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6/006
 * Time: 13:34
 */

namespace App\Http;


class Error
{
    /**
     * @var  string
     */
    public $LongMessage;

    /**
     * @var  string
     */
    public $ShortMessage;

    public function __construct(string $ShortMessage = null, string $LongMessage = null)
    {
        $this->LongMessage = $LongMessage;
        $this->ShortMessage = $ShortMessage;
    }
}