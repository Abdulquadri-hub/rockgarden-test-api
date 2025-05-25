<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\ReceiptEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\ReceiptCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReceiptEventListener
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
     * @param  \App\Events\ReceiptEvent  $event
     * @return void
     */
    public function handle(ReceiptEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::RECEIPT){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new ReceiptCreatedMail($dto->getPayerName(), $dto->getClientFullname(), $dto->getTransRef(), $dto->getReceiptId(), $dto->getTransDate(), $dto->getInvoiceName(), $dto->getAmount(), $dto->getCurrency(), $dto->getAmountPaid()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::receiptCreatedMessage($dto->getPayerName(), $dto->getClientFullname(), $dto->getTransRef(), $dto->getReceiptId(), $dto->getTransDate(), $dto->getInvoiceName(), $dto->getAmount(), $dto->getCurrency(), $dto->getAmountPaid()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
