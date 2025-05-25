<?php

namespace App\Dto;

class IncidentDto extends BaseDto
{
    private $client_fullname;

    /**
     * @param $client_fullname
     * @param string $type
     */
    public function __construct($client_fullname, string $type)
    {
        parent::setType($type);
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
}
