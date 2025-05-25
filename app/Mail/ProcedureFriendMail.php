<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProcedureFriendMail extends Mailable
{
    use Queueable, SerializesModels;

    private $family_friend_name, $item_name, $client_fullname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($family_friend_name, $item_name, $client_fullname)
    {
        $this->family_friend_name = $family_friend_name;
        $this->item_name = $item_name;
        $this->client_fullname = $client_fullname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $family_friend_name = $this->family_friend_name;
        $item_name = $this->item_name;
        $client_fullname = $this->client_fullname;

        return $this->subject('Procedure Friend')->view('emails.service.procedure-friend', compact( 'family_friend_name', 'item_name', 'client_fullname'));
    }
}
