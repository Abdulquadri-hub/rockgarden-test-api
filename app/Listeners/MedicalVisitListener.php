<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\MedicalVisitEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\MedicalVisitAdminMail;
use App\Mail\MedicalVisitFriendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MedicalVisitListener
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
     * @param  \App\Events\MedicalVisitEvent  $event
     * @return void
     */
    public function handle(MedicalVisitEvent $event)
    {
        $dto = $event->dto;


        try {
            $res = UserService::usersAdmin($dto->getType(), true);

            $contact = $res['contact'];
            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            $friends = UserService::familyFriendUsers($event->clientId);

            if($dto->getType() == EventType::MEDICAL_VISIT_ADMIN){
                if(!empty($contact)){
                    //Send Email
                    if (filter_var($contact['email'], FILTER_VALIDATE_EMAIL) && $send_email) {
                        Mail::to($contact['email'])->send(new MedicalVisitAdminMail($dto->getClientFullname(), $dto->getDashboardLink()));
                    }

                    // Send Sms to user
                    if(!empty($contact['phone']) && $send_sms){
                        TwilioSMSController::sendSMS($contact['phone'], TwilioSMSController::medicalVisitAdminMessage($dto->getClientFullname(), $dto->getDashboardLink()));
                    }
                }
            }else if($dto->getType() == EventType::MEDICAL_VISIT_FRIEND){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new MedicalVisitFriendMail($dto->getClientFullname(), $friend->first_name.' '.$friend->last_name));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::medicalVisitFriendMessage($dto->getClientFullname(), $friend->first_name.' '.$friend->last_name));
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
