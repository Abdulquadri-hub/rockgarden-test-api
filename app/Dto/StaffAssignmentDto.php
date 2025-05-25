<?php

namespace App\Dto;

class StaffAssignmentDto extends BaseDto
{
    private $client_fullname;
    private $fullname;

    /**
     * @param $client_fullname
     * @param string $type
     */
    public function __construct($client_fullname, $fullname, string $type)
    {
        parent::setType($type);
        $this->fullname = $fullname;
        $this->client_fullname = $client_fullname;
    }

    /**
     * @return mixed
     */
    public function getClientFullname()
    {
        return $this->client_fullname;
    }

    /**
     * @param mixed $client_fullname
     */
    public function setClientFullname($client_fullname): void
    {
        $this->client_fullname = $client_fullname;
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
}
