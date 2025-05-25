<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrescriptionAdminCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client_fullname, $dashboard_link, $medicine_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_fullname, $dashboard_link, $medicine_name)
    {
        $this->client_fullname = $client_fullname;
        $this->dashboard_link =  $dashboard_link;
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
        $dashboard_link =  $this->dashboard_link;
        $medicine_name = $this->medicine_name;
        return $this->subject('Prescription')->view('emails.medical.prescription-created-admin', compact(  'client_fullname', 'dashboard_link', 'medicine_name'));
    }
}
