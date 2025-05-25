<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\InvoiceEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\InvoiceCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceEventListener
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
     * @param  \App\Events\InvoiceEvent  $event
     * @return void
     */
    public function handle(InvoiceEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            $friends = UserService::familyFriendUsers($event->clientId);

            if($dto->getType() == EventType::INVOICE_CREATED){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new InvoiceCreatedMail($friend->first_name.' '.$friend->last_name, $dto->getClientFullname(), $dto->getInvoiceName(), $dto->getAmount(), $dto->getClientFullname(), $dto->getDueDate(), $dto->getPaymentLink()));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::invoiceCreatedMessage($friend->first_name.' '.$friend->last_name,$dto->getClientFullname(), $dto->getInvoiceName(), $dto->getAmount(), $dto->getClientFullname(), $dto->getDueDate(), $dto->getPaymentLink()));
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
