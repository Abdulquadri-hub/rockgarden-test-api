<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\InvoiceController;
use Symfony\Component\Console\Input\InputArgument;

class SendEmailInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
  protected $signature = 'email:send-invoice {client_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with invoice';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // \Log::info(1);
       $client_id = 103;
    

    // First, send USD invoices
    $request = new Request([
        'client_id' => $client_id,
     
        
    ]);

    $this->info(app(InvoiceController::class)->send_invoice_email($request));

   
    

    \Log::info("Invoice Cron is working fine!");
     
    }
}
