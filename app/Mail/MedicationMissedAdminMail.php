<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MedicationMissedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    private $client_fullname, $dashboard_link, $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client_fullname, $dashboard_link, $date)
    {
        $this->client_fullname = $client_fullname;
        $this->dashboard_link =  $dashboard_link;
        $this->date = $date;
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
        $date = $this->date;
        return $this->subject('Medication Missed')->view('emails.medical.medication-missed-admin', compact(  'client_fullname', 'dashboard_link', 'date'));
    }
}
