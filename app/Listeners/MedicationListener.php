<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\MedicationEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\MedicationMissedAdminMail;
use App\Mail\MedicationMissedFriendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MedicationListener
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
     * @param  \App\Events\MedicationEvent  $event
     * @return void
     */
    public function handle(MedicationEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), true);

            $contact = $res['contact'];
            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            $friends = UserService::familyFriendUsers($event->clientId);

            if($dto->getType() == EventType::MEDICATION_ADMIN){
                if(!empty($contact)){
                    //Send Email
                    if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && $send_email) {
                        Mail::to($contact['email'])->send(new MedicationMissedAdminMail($dto->getClientFullname(), $dto->getDashboardLink(), $dto->getDate()));
                    }

                    // Send Sms to user
                    if(!empty($contact['phone']) && $send_sms){
                        TwilioSMSController::sendSMS($contact['phone'], TwilioSMSController::medicationMissedAdminMessage($dto->getClientFullname(), $dto->getDashboardLink(), $dto->getDate()));
                    }
                }
            }else if($dto->getType() == EventType::MEDICATION_FRIEND){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new MedicationMissedFriendMail($dto->getClientFullname(), $friend->first_name.' '.$friend->last_name, $dto->getDate()));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::medicationMissedFriendMessage($dto->getClientFullname(), $friend->first_name.' '.$friend->last_name, $dto->getDate()));
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
