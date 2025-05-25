<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user_fullname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_fullname)
    {
         $this->user_fullname = $user_fullname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user_fullname = $this->user_fullname;
        return $this->subject('Account Activated')->view('emails.users.account-activated', compact('user_fullname'));
    }
}
