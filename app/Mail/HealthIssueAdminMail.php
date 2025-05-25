<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HealthIssueAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client_fullname, $dashboard_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_fullname, $dashboard_link)
    {
        $this->client_fullname = $client_fullname;
        $this->dashboard_link =  $dashboard_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_fullname = $this->client_fullname;
        $dashboard_link =  $this->dashboard_link;

        return $this->subject('health Issue')->view('emails.medical.health-issue-admin', compact(  'client_fullname', 'dashboard_link'));
    }
}
