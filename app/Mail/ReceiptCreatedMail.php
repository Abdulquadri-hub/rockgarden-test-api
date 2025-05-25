<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $payer_name, $client_fullname, $trans_ref, $receipt_id, $trans_date, $invoice_name, $amount, $currency, $amount_paid;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payer_name, $client_fullname, $trans_ref, $receipt_id, $trans_date, $invoice_name, $amount, $currency, $amount_paid)
    {
        $this->payer_name = $payer_name;
        $this->invoice_name = $invoice_name;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->client_fullname = $client_fullname;
        $this->trans_date = $trans_date;
        $this->receipt_id = $receipt_id;
        $this->trans_ref = $trans_ref;
        $this->amount_paid = $amount_paid;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_fullname = $this->client_fullname;
        $payer_name = $this->payer_name;
        $invoice_name = $this->invoice_name;
        $amount = $this->amount;
        $currency = $this->currency;
        $trans_date = $this->trans_date;
        $receipt_id = $this->receipt_id;
        $trans_ref = $this->trans_ref;
        $amount_paid = $this->amount_paid;

        return $this->subject('New Receipt')->view('emails.payments.receipt-created', compact( 'payer_name', 'invoice_name', 'client_fullname', 'currency', 'amount', 'trans_date', 'receipt_id', 'trans_ref', 'amount_paid'));
    }
}
