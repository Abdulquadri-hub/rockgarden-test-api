<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\DeathRecordEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\DeathRecordAdminMail;
use App\Mail\NewApplicationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DeathRecordEventListener
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
     * @param  \App\Events\DeathRecordEvent  $event
     * @return void
     */
    public function handle(DeathRecordEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), true);

            $contact = $res['contact'];
            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::DEATH_RECORD){
                $admins = UserService::usersAdmin();

                if(!empty($contact)){
                    //Send Email
                    if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && $send_email) {
                        Mail::to($contact['email'])->send(new DeathRecordAdminMail($dto->getClientFullname(), $dto->getDashboardLink()));
                    }

                    // Send Sms to user
                    if(!empty($contact['phone']) && $send_sms){
                        TwilioSMSController::sendSMS($contact['phone'], TwilioSMSController::deathRecordAdminMessage($dto->getClientFullname(), $dto->getDashboardLink()));
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }

    }
}
