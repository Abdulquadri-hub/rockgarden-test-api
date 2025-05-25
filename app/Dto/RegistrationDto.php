<?php

namespace App\Dto;

class RegistrationDto extends BaseDto
{
    private $name;

    /**
     * @param $name
     */
    public function __construct($name, EventType $eventType)
    {
        parent::setType($eventType);
        $this->name = $name;
    }


}
