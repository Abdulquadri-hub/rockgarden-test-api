<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $name, $digit_code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $digit_code)
    {
        $this->digit_code = $digit_code;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->name;
        $digit_code = $this->digit_code;

        return $this->subject('Reset Password')->view('emails.users.reset-password', compact('name', 'digit_code'));

    }
}
