<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\DocumentStaffEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\DocumentStaffCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DocumentStaffEventListener
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
     * @param  \App\Events\DocumentStaffEvent  $event
     * @return void
     */
    public function handle(DocumentStaffEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::DOCUMENT_STAFF_CREATED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new DocumentStaffCreatedMail($dto->getFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::documentStaffCreatedMessage($dto->getFullname()));
                }
            }else if($dto->getType() == EventType::DOCUMENT_STAFF_UPDATE){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new DocumentStaffCreatedMail($dto->getFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::documentStaffCreatedMessage($dto->getFullname()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
