<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrescriptionFriendCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client_fullname, $family_friend_name, $medicine_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_fullname, $family_friend_name, $medicine_name)
    {
        $this->client_fullname = $client_fullname;
        $this->family_friend_name =  $family_friend_name;
        $this->medicine_name = $medicine_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $client_fullname = $this->client_fullname;
        $family_friend_name =  $this->family_friend_name;
        $medicine_name = $this->medicine_name;
        return $this->subject('Prescription')->view('emails.medical.prescription-created-friend', compact(  'client_fullname', 'family_friend_name', 'medicine_name'));
    }
}
