<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    private $applicant_fullname, $dashboard_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($applicant_fullname, $dashboard_link)
    {
        $this->applicant_fullname = $applicant_fullname;
        $this->dashboard_link =  $dashboard_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $applicant_fullname = $this->applicant_fullname;
        $dashboard_link =  $this->dashboard_link;

        return $this->subject('New Application')->view('emails.service.new-application', compact(  'applicant_fullname', 'dashboard_link'));
    }
}
