<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    private $item_name, $total_item_in_stock, $dashboard_link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item_name, $total_item_in_stock, $dashboard_link)
    {
        $this->item_name = $item_name;
        $this->total_item_in_stock = $total_item_in_stock;
        $this->dashboard_link = $dashboard_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $item_name = $this->item_name;
        $total_item_in_stock = $this->total_item_in_stock;
        $dashboard_link =  $this->dashboard_link;

        return $this->subject('Low Stock')->view('emails.service.low-stock', compact( 'item_name', 'total_item_in_stock', 'dashboard_link'));
    }
}
