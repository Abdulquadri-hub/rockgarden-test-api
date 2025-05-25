<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptAfterPayMail extends Mailable
{
    use Queueable, SerializesModels;
    private $payer_name, $client_fullname, $trans_ref, $trans_date, $amount, $currency,$staffDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payer_name, $client_fullname, $trans_ref,  $trans_date, $amount, $currency,$staffDetails)
    {
        $this->payer_name = $payer_name;
        
        $this->amount = $amount;
        $this->currency = $currency;
        $this->client_fullname = $client_fullname;
        $this->trans_date = $trans_date;
        $this->staffDetails = $staffDetails;
       
        $this->trans_ref = $trans_ref;
       
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
        
        $amount = $this->amount;
        $currency = $this->currency;
        $trans_date = $this->trans_date;
        $staffDetails= $this->staffDetails;
        $trans_ref = $this->trans_ref;
       

        return $this->subject('Payment Receipt')->view('emails.payments.receipt_after_pay', compact( 'payer_name', 'client_fullname', 'currency', 'amount', 'trans_date', 'trans_ref','staffDetails'));
    }
}
