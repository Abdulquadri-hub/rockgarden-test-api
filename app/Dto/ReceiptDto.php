<?php

namespace App\Dto;

class ReceiptDto extends BaseDto
{
    private $payer_name;
    private $client_fullname;
    private $trans_ref;
    private $receipt_id;
    private $trans_date;
    private $invoice_name;
    private $amount;
    private $currency;
    private $amount_paid;

    /**
     * @param $payer_name
     * @param $client_fullname
     * @param $trans_ref
     * @param $receipt_id
     * @param $trans_date
     * @param $invoice_name
     * @param $amount
     * @param $currency
     * @param $amount_paid
     */
    public function __construct($payer_name, $client_fullname, $trans_ref, $receipt_id, $trans_date, $invoice_name, $amount, $currency, $amount_paid, string $type)
    {
        parent::setType($type);
        $this->payer_name = $payer_name;
        $this->client_fullname = $client_fullname;
        $this->trans_ref = $trans_ref;
        $this->receipt_id = $receipt_id;
        $this->trans_date = $trans_date;
        $this->invoice_name = $invoice_name;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->amount_paid = $amount_paid;
    }

    /**
     * @return mixed
     */
    public function getPayerName()
    {
        return $this->payer_name;
    }

    /**
     * @param mixed $payer_name
     */
    public function setPayerName($payer_name): void
    {
        $this->payer_name = $payer_name;
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
    public function getTransRef()
    {
        return $this->trans_ref;
    }

    /**
     * @param mixed $trans_ref
     */
    public function setTransRef($trans_ref): void
    {
        $this->trans_ref = $trans_ref;
    }

    /**
     * @return mixed
     */
    public function getReceiptId()
    {
        return $this->receipt_id;
    }

    /**
     * @param mixed $receipt_id
     */
    public function setReceiptId($receipt_id): void
    {
        $this->receipt_id = $receipt_id;
    }

    /**
     * @return mixed
     */
    public function getTransDate()
    {
        return $this->trans_date;
    }

    /**
     * @param mixed $trans_date
     */
    public function setTransDate($trans_date): void
    {
        $this->trans_date = $trans_date;
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
    public function getAmountPaid()
    {
        return $this->amount_paid;
    }

    /**
     * @param mixed $amount_paid
     */
    public function setAmountPaid($amount_paid): void
    {
        $this->amount_paid = $amount_paid;
    }
}
