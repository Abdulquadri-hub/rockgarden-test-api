<?php

namespace App\Dto;

class FamilyFriendDto extends BaseDto
{
    private $familyfriend_name;

    private $client_fullname;

    /**
     * @param $familyfriend_name
     * @param $client_fullname
     * @param string $type
     */
    public function __construct($familyfriend_name, $client_fullname, string $type)
    {
        parent::setType($type);
        $this->familyfriend_name = $familyfriend_name;
        $this->client_fullname = $client_fullname;
    }

    /**
     * @return mixed
     */
    public function getFamilyfriendName()
    {
        return $this->familyfriend_name;
    }

    /**
     * @param mixed $familyfriend_name
     */
    public function setFamilyfriendName($familyfriend_name): void
    {
        $this->familyfriend_name = $familyfriend_name;
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
