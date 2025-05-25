<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\ApplicationEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\ApplicationApprovedMail;
use App\Mail\ApplicationRejectedMail;
use App\Mail\NewAccountMail;
use App\Mail\NewApplicationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApplicationEventListener
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
     * @param  \App\Events\ApplicationEvent  $event
     * @return void
     */
    public function handle(ApplicationEvent $event)
    {
        $type = $event->dto->getType();
        $dto = $event->dto;



        try {
            $res = UserService::usersAdmin($dto->getType(), true);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($type == EventType::APPLICATION_NEW){

                if(!empty($admins)){
                    foreach ($admins as $admin){
                        //Send Email
                        if (filter_var($admin->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($admin->email)->send(new NewApplicationMail($dto->getApplicantFullname(), $dto->getDashboardLink()));
                        }

                        // Send Sms to user
                        if(!empty($admin->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($admin->phone_num, TwilioSMSController::newApplicationMessage($dto->getApplicantFullname(), $dto->getDashboardLink()));
                        }
                    }
                }

            }else if ($type == EventType::APPLICATION_APPROVED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL)  && $send_email) {
                    Mail::to($event->email)->send(new ApplicationApprovedMail($dto->getApplicantFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::applicationApprovedMessage($dto->getApplicantFullname()));
                }
            }else if($type == EventType::APPLICATION_REJECTED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new ApplicationRejectedMail($dto->getApplicantFullname(), $dto->getReasonForRejection()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::applicationRejectedMessage($dto->getApplicantFullname(), $dto->getReasonForRejection()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
