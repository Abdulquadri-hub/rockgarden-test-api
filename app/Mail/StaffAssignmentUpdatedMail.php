<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffAssignmentUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $fullname;

    private $client_fullname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $client_fullname)
    {
        $this->fullname = $fullname;
        $this->client_fullname = $client_fullname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fullname = $this->fullname;

        $client_fullname = $this->client_fullname;

        return $this->subject('Staff Assignment')->view('emails.assignment.staff-assignment-updated', compact('fullname', 'client_fullname'));

    }
}
