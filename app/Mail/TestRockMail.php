<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestRockMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $receiver;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($receiver)
    {
        //
        $this->receiver = $receiver;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        $name = $this->name;
//        $id = $this->news->id;
//        $title = $this->news->title;
        return $this->subject('Rock Garden Mail me')->view('emails.mail');
    }
}
