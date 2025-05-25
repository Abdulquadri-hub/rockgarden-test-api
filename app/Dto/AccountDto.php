<?php

namespace App\Dto;

class AccountDto extends BaseDto
{
    private $user_fullname;
    private $name;
    private $digit_code;
    private $fullname;
    private $email;
    private $password;

    /**
     * @param $user_fullname
     * @param $name
     * @param $digit_code
     * @param $fullname
     * @param $email
     * @param $password
     * @param $eventType
     */
    public function __construct($user_fullname, $name, $digit_code, $fullname, $email, $password, $eventType)
    {
        parent::setType($eventType);
        $this->user_fullname = $user_fullname;
        $this->name = $name;
        $this->digit_code = $digit_code;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getUserFullname()
    {
        return $this->user_fullname;
    }

    /**
     * @param mixed $user_fullname
     */
    public function setUserFullname($user_fullname): void
    {
        $this->user_fullname = $user_fullname;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDigitCode()
    {
        return $this->digit_code;
    }

    /**
     * @param mixed $digit_code
     */
    public function setDigitCode($digit_code): void
    {
        $this->digit_code = $digit_code;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname): void
    {
        $this->fullname = $fullname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }
}
