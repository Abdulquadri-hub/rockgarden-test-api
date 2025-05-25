<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FamilyFriendCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $familyfriend_name;

    private $client_fullname;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($familyfriend_name, $client_fullname)
    {
         $this->familyfriend_name = $familyfriend_name;
         $this->client_fullname = $client_fullname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $familyfriend_name = $this->familyfriend_name;

        $client_fullname = $this->client_fullname;

        return $this->subject('New Family Friend')->view('emails.assignment.family-friend-created', compact('familyfriend_name', 'client_fullname'));

    }
}
