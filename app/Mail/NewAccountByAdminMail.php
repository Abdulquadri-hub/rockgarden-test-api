<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAccountByAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    private $fullname, $email, $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $email, $password)
    {
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fullname = $this->fullname;

        $email = $this->email;

        $password = $this->password;

        return $this->subject('New Account')->view('emails.users.new-account-by-admin', compact('fullname', 'email', 'password'));

    }
}
