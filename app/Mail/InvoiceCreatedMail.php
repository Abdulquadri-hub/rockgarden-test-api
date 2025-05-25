<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $family_friend_name, $client_fullname, $invoice_name, $amount, $currency, $due_date, $payment_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($family_friend_name,$client_fullname, $invoice_name, $amount, $currency, $due_date, $payment_link)
    {
        $this->invoice_name = $invoice_name;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->due_date = $due_date;
        $this->payment_link = $payment_link;
        $this->client_fullname = $client_fullname;
        $this->family_friend_name = $family_friend_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_fullname = $this->client_fullname;
        $invoice_name = $this->invoice_name;
        $amount = $this->amount;
        $currency = $this->currency;
        $due_date = $this->due_date;
        $payment_link = $this->payment_link;
        $family_friend_name =  $this->family_friend_name;

        return $this->subject('New Invoice')->view('emails.payments.invoice-created', compact(  'family_friend_name','invoice_name', 'client_fullname', 'currency', 'amount', 'due_date', 'payment_link'));
    }
}
