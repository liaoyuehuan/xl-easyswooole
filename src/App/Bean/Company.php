<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26
 * Time: 10:42
 */

namespace App\Bean;


use Core\Component\Spl\SplBean;

class Company extends SplBean
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $app_id;

    /**
     * @var string
     */
    protected $app_secret;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $tel;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $remark;


    /**
     * @var int
     */
    protected $status;

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
     * @return int
     */
    public function getAppId(): ?int
    {
        return $this->app_id;
    }

    /**
     * @param int $app_id
     */
    public function setAppId(?int $app_id): void
    {
        $this->app_id = $app_id;
    }

    /**
     * @return string
     */
    public function getAppSecret(): ?string
    {
        return $this->app_secret;
    }

    /**
     * @param string $app_secret
     */
    public function setAppSecret(?string $app_secret): void
    {
        $this->app_secret = $app_secret;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getTel(): ?string
    {
        return $this->tel;
    }

    /**
     * @param string $tel
     */
    public function setTel(?string $tel): void
    {
        $this->tel = $tel;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
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