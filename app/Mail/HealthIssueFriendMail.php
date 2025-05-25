<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HealthIssueFriendMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client_fullname, $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_fullname, $name)
    {
        $this->client_fullname = $client_fullname;
        $this->name =  $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_fullname = $this->client_fullname;
        $name =  $this->name;
        return $this->subject('Health Issue')->view('emails.medical.health-issue-friend', compact(  'client_fullname', 'name'));
    }
}
