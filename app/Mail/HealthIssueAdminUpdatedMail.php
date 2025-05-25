<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HealthIssueAdminUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client_fullname, $health_issue_name, $dashboard_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_fullname, $health_issue_name, $dashboard_link)
    {
        $this->client_fullname = $client_fullname;
        $this->dashboard_link =  $dashboard_link;
        $this->health_issue_name =  $health_issue_name;
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
        $health_issue_name =  $this->health_issue_name;
        return $this->subject('Health Issue')->view('emails.medical.health-issue-update-admin', compact(  'health_issue_name','client_fullname', 'dashboard_link'));
    }
}
