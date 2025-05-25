<?php

namespace App\Dto;

class ResetPasswordDto extends BaseDto
{
    private $name;
    private $digit_code;

    /**
     * @param $name
     * @param $digit_code
     * @param string $type
     */
    public function __construct($name, $digit_code, string $type)
    {
        parent::setType($type);
        $this->name = $name;
        $this->digit_code = $digit_code;
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
}
