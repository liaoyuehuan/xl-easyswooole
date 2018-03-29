<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15/015
 * Time: 9:03
 */
namespace App\Bean;

use Core\Component\Spl\SplBean;

class Token extends SplBean
{
    /**
     * @var string
     */
    protected $access_token;

    /**
     * @var int
     */
    protected $w1_valid;


    /**
     * @var string
     */
    protected $refresh_token;

    /**
     * @var int
     */
    protected $refresh_token_valid_time;

    /**
     * @var
     */
    protected $w2_valid;

    /**
     * @var string
     */
    protected $user_id;

    /**
     * @var int
     */
    protected $expire_time;

    /**
     * @var int
     */
    protected $r2_valid;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var int
     */
    protected $r1_valid;

    /**
     * @var string
     */
    protected $sp;

    /**
     * @var string
     */
    protected $user_nick;

    /**
     * @var int
     */
    protected $company_id;

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
        empty($this->s) && $this->sp = 'ae';
        empty($this->c_time) && $this->c_time = time();
        empty($this->c_time) &&  $this->m_time = time();
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @param string $access_token
     */
    public function setAccessToken(string $access_token): void
    {
        $this->access_token = $access_token;
    }

    /**
     * @return int
     */
    public function getW1Valid(): int
    {
        return $this->w1_valid;
    }

    /**
     * @param int $w1_valid
     */
    public function setW1Valid(int $w1_valid): void
    {
        $this->w1_valid = $w1_valid;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     * @param mixed $refresh_token
     */
    public function setRefreshToken($refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }



    /**
     * @return int
     */
    public function getRefreshTokenValidTime(): int
    {
        return $this->refresh_token_valid_time;
    }

    /**
     * @param int $refresh_token_valid_time
     */
    public function setRefreshTokenValidTime(int $refresh_token_valid_time): void
    {
        $this->refresh_token_valid_time = $refresh_token_valid_time;
    }

    /**
     * @return mixed
     */
    public function getW2Valid()
    {
        return $this->w2_valid;
    }

    /**
     * @param mixed $w2_valid
     */
    public function setW2Valid($w2_valid): void
    {
        $this->w2_valid = $w2_valid;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     */
    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getExpireTime(): int
    {
        return $this->expire_time;
    }

    /**
     * @param int $expire_time
     */
    public function setExpireTime(int $expire_time): void
    {
        $this->expire_time = $expire_time;
    }

    /**
     * @return int
     */
    public function getR2Valid(): int
    {
        return $this->r2_valid;
    }

    /**
     * @param int $r2_valid
     */
    public function setR2Valid(int $r2_valid): void
    {
        $this->r2_valid = $r2_valid;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return int
     */
    public function getR1Valid(): int
    {
        return $this->r1_valid;
    }

    /**
     * @param int $r1_valid
     */
    public function setR1Valid(int $r1_valid): void
    {
        $this->r1_valid = $r1_valid;
    }

    /**
     * @return string
     */
    public function getSp(): string
    {
        return $this->sp;
    }

    /**
     * @param string $sp
     */
    public function setSp(string $sp): void
    {
        $this->sp = $sp;
    }

    /**
     * @return string
     */
    public function getUserNick(): string
    {
        return $this->user_nick;
    }

    /**
     * @param string $user_nick
     */
    public function setUserNick($user_nick): void
    {
        $this->user_nick = $user_nick;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->company_id;
    }

    /**
     * @param int $company_id
     */
    public function setCompanyId($company_id): void
    {
        $this->company_id = $company_id;
    }

    /**
     * @return int
     */
    public function getCTime()
    {
        return $this->c_time;
    }

    /**
     * @param int $c_time
     */
    public function setCTime($c_time): void
    {
        $this->c_time = $c_time;
    }

    /**
     * @return int
     */
    public function getMTime()
    {
        return $this->m_time;
    }

    /**
     * @param int $m_time
     */
    public function setMTime($m_time): void
    {
        $this->m_time = $m_time;
    }

}