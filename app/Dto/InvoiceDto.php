<?php

namespace App\Dto;

class InvoiceDto extends BaseDto
{
    private $client_fullname;
    private $invoice_name;
    private $amount;
    private $currency;
    private $due_date;
    private $payment_link;

    /**
     * @param $client_fullname
     * @param $invoice_name
     * @param $amount
     * @param $currency
     * @param $due_date
     * @param $payment_link
     * @param string $type
     */
    public function __construct($client_fullname, $invoice_name, $amount, $currency, $due_date, $payment_link, string $type)
    {
        parent::setType($type);
        $this->client_fullname = $client_fullname;
        $this->invoice_name = $invoice_name;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->due_date = $due_date;
        $this->payment_link = $payment_link;
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
    public function getInvoiceName()
    {
        return $this->invoice_name;
    }

    /**
     * @param mixed $invoice_name
     */
    public function setInvoiceName($invoice_name): void
    {
        $this->invoice_name = $invoice_name;
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
    public function getDueDate()
    {
        return $this->due_date;
    }

    /**
     * @param mixed $due_date
     */
    public function setDueDate($due_date): void
    {
        $this->due_date = $due_date;
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
