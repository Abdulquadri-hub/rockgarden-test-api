<?php

namespace App\Dto;

class MedicalVisitDto extends BaseDto
{
    private $client_fullname;
    private $family_friend_name;

    /**
     * @param $client_fullname
     * @param $family_friend_name
     * @param string $type
     */
    public function __construct($client_fullname, $family_friend_name, string $type)
    {
        parent::setType($type);
        $this->client_fullname = $client_fullname;
        $this->family_friend_name = $family_friend_name;
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
    public function getFamilyFriendName()
    {
        return $this->family_friend_name;
    }

    /**
     * @param mixed $family_friend_name
     */
    public function setFamilyFriendName($family_friend_name): void
    {
        $this->family_friend_name = $family_friend_name;
    }
}
