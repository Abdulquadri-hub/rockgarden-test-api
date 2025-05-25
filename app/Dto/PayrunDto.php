<?php

namespace App\Dto;

class PayrunDto extends BaseDto
{
    private $department_name;
    private $taxes;
    private $deductions;
    private $payrun_title;
    private $allowances;
    private $bonuses;
    private $date_to;
    private $date_from;
    private $total_amount;
    private $total_currency;
    private $account_no;
    private $duty_type;
    private $bank_name;
    private $designation_name;
    private $empoyee_fullname;
    private $amount;
    private $currency;
    private $payment_link;

    /**
     * @param $department_name
     * @param $taxes
     * @param $deductions
     * @param $payrun_title
     * @param $allowances
     * @param $bonuses
     * @param $date_to
     * @param $date_from
     * @param $total_amount
     * @param $total_currency
     * @param $account_no
     * @param $duty_type
     * @param $bank_name
     * @param $designation_name
     * @param $empoyee_fullname
     * @param $amount
     * @param $currency
     * @param $payment_link
     * @param string $type
     */
    public function __construct($department_name, $taxes, $deductions, $allowances, $bonuses, $payrun_title, $date_to, $date_from, $total_amount, $total_currency, $account_no, $duty_type, $bank_name, $designation_name, $empoyee_fullname, $amount, $currency, $payment_link, string $type)
    {
        parent::setType($type);
        $this->department_name = $department_name;
        $this->taxes = $taxes;
        $this->deductions = $deductions;
        $this->payrun_title = $payrun_title;
        $this->allowances = $allowances;
        $this->bonuses = $bonuses;
        $this->date_to = $date_to;
        $this->date_from = $date_from;
        $this->total_amount = $total_amount;
        $this->total_currency = $total_currency;
        $this->account_no = $account_no;
        $this->duty_type = $duty_type;
        $this->bank_name = $bank_name;
        $this->designation_name = $designation_name;
        $this->empoyee_fullname = $empoyee_fullname;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->payment_link = $payment_link;
    }

    /**
     * @return mixed
     */
    public function getDepartmentName()
    {
        return $this->department_name;
    }

    /**
     * @param mixed $department_name
     */
    public function setDepartmentName($department_name): void
    {
        $this->department_name = $department_name;
    }

    /**
     * @return mixed
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @param mixed $taxes
     */
    public function setTaxes($taxes): void
    {
        $this->taxes = $taxes;
    }

    /**
     * @return mixed
     */
    public function getDeductions()
    {
        return $this->deductions;
    }

    /**
     * @param mixed $deductions
     */
    public function setDeductions($deductions): void
    {
        $this->deductions = $deductions;
    }

    /**
     * @return mixed
     */
    public function getPayrunTitle()
    {
        return $this->payrun_title;
    }

    /**
     * @param mixed $payrun_title
     */
    public function setPayrunTitle($payrun_title): void
    {
        $this->payrun_title = $payrun_title;
    }

    /**
     * @return mixed
     */
    public function getAllowances()
    {
        return $this->allowances;
    }

    /**
     * @param mixed $allowances
     */
    public function setAllowances($allowances): void
    {
        $this->allowances = $allowances;
    }

    /**
     * @return mixed
     */
    public function getBonuses()
    {
        return $this->bonuses;
    }

    /**
     * @param mixed $bonuses
     */
    public function setBonuses($bonuses): void
    {
        $this->bonuses = $bonuses;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->date_to;
    }

    /**
     * @param mixed $date_to
     */
    public function setDateTo($date_to): void
    {
        $this->date_to = $date_to;
    }

    /**
     * @return mixed
     */
    public function getDateFrom()
    {
        return $this->date_from;
    }

    /**
     * @param mixed $date_from
     */
    public function setDateFrom($date_from): void
    {
        $this->date_from = $date_from;
    }

    /**
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->total_amount;
    }

    /**
     * @param mixed $total_amount
     */
    public function setTotalAmount($total_amount): void
    {
        $this->total_amount = $total_amount;
    }

    /**
     * @return mixed
     */
    public function getTotalCurrency()
    {
        return $this->total_currency;
    }

    /**
     * @param mixed $total_currency
     */
    public function setTotalCurrency($total_currency): void
    {
        $this->total_currency = $total_currency;
    }

    /**
     * @return mixed
     */
    public function getAccountNo()
    {
        return $this->account_no;
    }

    /**
     * @param mixed $account_no
     */
    public function setAccountNo($account_no): void
    {
        $this->account_no = $account_no;
    }

    /**
     * @return mixed
     */
    public function getDutyType()
    {
        return $this->duty_type;
    }

    /**
     * @param mixed $duty_type
     */
    public function setDutyType($duty_type): void
    {
        $this->duty_type = $duty_type;
    }

    /**
     * @return mixed
     */
    public function getBankName()
    {
        return $this->bank_name;
    }

    /**
     * @param mixed $bank_name
     */
    public function setBankName($bank_name): void
    {
        $this->bank_name = $bank_name;
    }

    /**
     * @return mixed
     */
    public function getDesignationName()
    {
        return $this->designation_name;
    }

    /**
     * @param mixed $designation_name
     */
    public function setDesignationName($designation_name): void
    {
        $this->designation_name = $designation_name;
    }

    /**
     * @return mixed
     */
    public function getEmpoyeeFullname()
    {
        return $this->empoyee_fullname;
    }

    /**
     * @param mixed $empoyee_fullname
     */
    public function setEmpoyeeFullname($empoyee_fullname): void
    {
        $this->empoyee_fullname = $empoyee_fullname;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getPaymentLink()
    {
        return $this->payment_link;
    }

    /**
     * @param mixed $payment_link
     */
    public function setPaymentLink($payment_link): void
    {
        $this->payment_link = $payment_link;
    }
}
