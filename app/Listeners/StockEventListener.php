<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\LowStockEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\LowStockMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StockEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LowStockEvent  $event
     * @return void
     */
    public function handle(LowStockEvent $event)
    {
        try {
            $dto = $event->dto;

            $res = UserService::usersAdmin($dto->getType(), true);

            $contact = $res['contact'];
            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::LOW_STOCK){

                if(!empty($contact)){
                    //Send Email
                    if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && $send_email) {
                        Mail::to($contact['email'])->send(new LowStockMail($dto->getItemName(), $dto->getTotalItemInStock(), $dto->getDashboardLink()));
                    }

                    // Send Sms to user
                    if(!empty($contact['phone']) && $send_sms){
                        TwilioSMSController::sendSMS($contact['phone'], TwilioSMSController::lowStockMessage($dto->getItemName(), $dto->getTotalItemInStock(), $dto->getDashboardLink()));
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
