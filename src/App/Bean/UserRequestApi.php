<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/23
 * Time: 10:11
 */

namespace App\Bean;


use Core\Component\Spl\SplBean;

class UserRequestApi extends SplBean
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
    protected $history_times;

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
        empty($this->m_time) &&  $this->m_time = time();
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
    public function getHistoryTimes(): ?int
    {
        return $this->history_times;
    }

    /**
     * @param int $history_times
     */
    public function setHistoryTimes(?int $history_times): void
    {
        $this->history_times = $history_times;
    }

    /**
     * @return int
     */
    public function getCTime():?int
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