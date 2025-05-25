<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProcedureAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    private $name, $item_name, $client_fullname, $dashboard_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $item_name, $client_fullname, $dashboard_link)
    {
        $this->name = $name;
        $this->item_name = $item_name;
        $this->client_fullname = $client_fullname;
        $this->dashboard_link =  $dashboard_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->name;
        $item_name = $this->item_name;
        $client_fullname = $this->client_fullname;
        $dashboard_link =  $this->dashboard_link;

        return $this->subject('Procedure Admin')->view('emails.service.procedure-admin', compact( 'name', 'item_name', 'client_fullname', 'dashboard_link'));
    }
}
