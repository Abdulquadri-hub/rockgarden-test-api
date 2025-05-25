<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayRunMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    public $attachment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $attachment)
    {
       $this->details = $details;
        $this->attachment = $attachment;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "PayRun" ;
    
    return $this->subject($subject)
        ->view('emails.payments.payrun')
        ->attachData($this->attachment['data'], $this->attachment['name']);
    }
}
