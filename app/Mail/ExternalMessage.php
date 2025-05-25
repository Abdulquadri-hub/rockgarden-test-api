<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExternalMessage extends Mailable
{
    use Queueable, SerializesModels;
    
    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject($this->message->subject)->view('emails.external-message');

        foreach ($this->message->attachments as $attachment) {
            $path = storage_path('app/public/' . $attachment->file_path);
            $mail->attach($path, [
                'as' => $attachment->file_name,
                'mime' => $attachment->mime_type
            ]);
        }

        return $mail;
    }
}
