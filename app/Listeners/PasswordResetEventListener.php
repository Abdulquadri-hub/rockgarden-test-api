<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\PasswordResetEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\ResetPasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetEventListener
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
     * @param  \App\Events\PasswordResetEvent  $event
     * @return void
     */
    public function handle(PasswordResetEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::PASSWORD_RESET){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new ResetPasswordMail($dto->getName(), $dto->getDigitCode()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::resetPasswordMessage($dto->getName(), $dto->getDigitCode()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
