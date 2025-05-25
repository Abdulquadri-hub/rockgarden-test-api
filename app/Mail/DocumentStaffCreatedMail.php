<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentStaffCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $fullname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $fullname = $this->fullname;

        return $this->subject('Staff Assignment')->view('emails.document.document-staff-created', compact( 'fullname'));

    }
}
