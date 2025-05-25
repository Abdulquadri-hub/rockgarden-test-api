<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $name, $reason_for_rejection;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $reason_for_rejection)
    {
        $this->name = $name;
        $this->reason_for_rejection = $reason_for_rejection;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->name;
        $reason_for_rejection = $this->reason_for_rejection;

        return $this->subject('Application Rejected')->view('emails.service.application-rejected', compact( 'name', 'reason_for_rejection'));
    }
}
