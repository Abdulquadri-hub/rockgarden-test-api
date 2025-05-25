<?php

namespace App\Dto;

class ApplicationDto extends BaseDto
{
    private $applicant_fullname;
    private $name;
    private $reason_for_rejection;

    /**
     * @param $applicant_fullname
     * @param $name
     * @param $reason_for_rejection
     * @param string $type
     */
    public function __construct($applicant_fullname, $name, $reason_for_rejection, string $type)
    {
        parent::setType($type);
        $this->applicant_fullname = $applicant_fullname;
        $this->name = $name;
        $this->reason_for_rejection = $reason_for_rejection;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getApplicantFullname()
    {
        return $this->applicant_fullname;
    }

    /**
     * @param mixed $applicant_fullname
     */
    public function setApplicantFullname($applicant_fullname): void
    {
        $this->applicant_fullname = $applicant_fullname;
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
    public function getReasonForRejection()
    {
        return $this->reason_for_rejection;
    }

    /**
     * @param mixed $reason_for_rejection
     */
    public function setReasonForRejection($reason_for_rejection): void
    {
        $this->reason_for_rejection = $reason_for_rejection;
    }
}
