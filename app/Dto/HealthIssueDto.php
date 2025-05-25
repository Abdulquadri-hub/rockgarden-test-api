<?php

namespace App\Dto;

class HealthIssueDto extends BaseDto
{
    private $client_fullname;
    private $health_issue_name;
    private $name;

    /**
     * @param $client_fullname
     * @param $health_issue_name
     * @param $name
     * @param string $type
     */
    public function __construct($client_fullname, $health_issue_name, $name, string $type)
    {
        parent::setType($type);
        $this->client_fullname = $client_fullname;
        $this->health_issue_name = $health_issue_name;
        $this->name = $name;
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
    public function getHealthIssueName()
    {
        return $this->health_issue_name;
    }

    /**
     * @param mixed $health_issue_name
     */
    public function setHealthIssueName($health_issue_name): void
    {
        $this->health_issue_name = $health_issue_name;
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
}
