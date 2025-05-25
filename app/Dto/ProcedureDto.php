<?php

namespace App\Dto;

class ProcedureDto extends BaseDto
{
    private $name;
    private $item_name;
    private $client_fullname;
    private $family_friend_name;

    /**
     * @param $name
     * @param $item_name
     * @param $client_fullname
     * @param $family_friend_name
     * @param string $type
     */
    public function __construct($name, $item_name, $client_fullname, $family_friend_name, string $type)
    {
        parent::setType($type);
        $this->name = $name;
        $this->item_name = $item_name;
        $this->client_fullname = $client_fullname;
        $this->family_friend_name = $family_friend_name;
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
    public function getItemName()
    {
        return $this->item_name;
    }

    /**
     * @param mixed $item_name
     */
    public function setItemName($item_name): void
    {
        $this->item_name = $item_name;
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
