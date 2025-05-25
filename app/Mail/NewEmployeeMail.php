<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmployeeMail extends Mailable
{
    use Queueable, SerializesModels;

    private $name, $email, $password, $roles;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $password, $roles)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->name;
        $email = $this->email;
        $password = $this->password;
        $roles = $this->roles;

        return $this->subject('New Account')->view('emails.users.new-employee', compact('name', 'email', 'password', 'roles'));

    }
}
