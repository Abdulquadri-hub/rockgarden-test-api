<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\NewAccountEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\NewAccountByAdminMail;
use App\Mail\NewAccountMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewAccountEventListener
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
     * @param  \App\Events\NewAccountEvent  $event
     * @return void
     */
    public function handle(NewAccountEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::NEW_ACCOUNT){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new NewAccountMail($dto->getFullname(), $dto->otp));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::newAccountMessage($dto->getFullname(), $dto->otp));
                }
            }else if($dto->getType() == EventType::ACCOUNT_BY_ADMIN){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new NewAccountByAdminMail($dto->getFullname(), $dto->getEmail(), $dto->getPassword()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::newAccountByAdminMessage($dto->getFullname(), $dto->getEmail(), $dto->getPassword()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
