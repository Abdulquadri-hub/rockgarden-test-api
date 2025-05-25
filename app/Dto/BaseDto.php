<?php

namespace App\Dto;

class BaseDto
{
    protected $dashboard_link;
    protected $type;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->dashboard_link = env('DASHBOARD_LINK');;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDashboardLink()
    {
        return $this->dashboard_link;
    }

    /**
     * @param mixed $dashboard_link
     */
    public function setDashboardLink($dashboard_link): void
    {
        $this->dashboard_link = $dashboard_link;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }
}
