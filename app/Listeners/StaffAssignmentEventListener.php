<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\StaffAssignmentEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\StaffAssignmentCreatedMail;
use App\Mail\StaffAssignmentUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StaffAssignmentEventListener
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
     * @param  \App\Events\StaffAssignmentEvent  $event
     * @return void
     */
    public function handle(StaffAssignmentEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::STAFF_ASSIGNMENT_CREATED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new StaffAssignmentCreatedMail($dto->getFullname(), $dto->getClientFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::staffAssignmentCreatedMessage($dto->getFullname(), $dto->getClientFullname()));
                }
            }else if($dto->getType() == EventType::STAFF_ASSIGNMENT_UPDATED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new StaffAssignmentUpdatedMail($dto->getFullname(), $dto->getClientFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::staffAssignmentUpdatedMessage($dto->getFullname(), $dto->getClientFullname()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
