<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public $details;
    public $attachment;
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
        $subject = ($this->details['category'] == 'Homes') ? "Rockgarden Homes Invoices" : "Rockgarden Homecare Agency Invoices";
    
    return $this->subject($subject)
        ->view('emails.invoice')
        ->attachData($this->attachment['data'], $this->attachment['name']);
    }
}
