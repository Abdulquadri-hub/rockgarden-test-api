<?php

namespace App\Dto;

class PrescriptionDto extends BaseDto
{
    private $client_fullname;

    private $medicine_name;

    private $family_friend_name;

    /**
     * @param $client_fullname
     * @param $medicine_name
     * @param $family_friend_name
     */
    public function __construct($client_fullname, $medicine_name, $family_friend_name, string $type)
    {
        parent::setType($type);
        $this->client_fullname = $client_fullname;
        $this->medicine_name = $medicine_name;
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
    public function getMedicineName()
    {
        return $this->medicine_name;
    }

    /**
     * @param mixed $medicine_name
     */
    public function setMedicineName($medicine_name): void
    {
        $this->medicine_name = $medicine_name;
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
