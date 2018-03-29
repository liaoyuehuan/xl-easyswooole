<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 11:32
 */

namespace App\Bean;


use Core\Component\Spl\SplBean;

class UserRequestApiDay extends SplBean
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $account;

    /**
     * @var int
     */
    protected $times;

    /**
     * @var string
     */
    protected $record_date;

    /**
     * @var int
     */
    protected $c_time;

    /**
     * @var int
     */
    protected $m_time;

    protected function initialize()
    {
        empty($this->c_time) && $this->c_time = time();
        empty($this->m_time) && $this->m_time = time();
        //误差取值
        empty($this->record_date) && $this->record_date = date('Y-m-d', time() - 3600 * 1);
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAccount(): ?string
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount(?string $account): void
    {
        $this->account = $account;
    }

    /**
     * @return int
     */
    public function getTimes(): ?int
    {
        return $this->times;
    }

    /**
     * @param int $times
     */
    public function setTimes(?int $times): void
    {
        $this->times = $times;
    }

    /**
     * @return string
     */
    public function getRecordDate(): ?string
    {
        return $this->record_date;
    }

    /**
     * @param string $record_date
     */
    public function setRecordDate(?string $record_date): void
    {
        $this->record_date = $record_date;
    }


    /**
     * @return int
     */
    public function getCTime(): ?int
    {
        return $this->c_time;
    }

    /**
     * @param int $c_time
     */
    public function setCTime(?int $c_time): void
    {
        $this->c_time = $c_time;
    }

    /**
     * @return int
     */
    public function getMTime(): ?int
    {
        return $this->m_time;
    }

    /**
     * @param int $m_time
     */
    public function setMTime(?int $m_time): void
    {
        $this->m_time = $m_time;
    }

}