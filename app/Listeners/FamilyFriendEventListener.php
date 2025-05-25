<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\FamilyFriendEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\FamilyFriendCreatedMail;
use App\Mail\FamilyFriendUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FamilyFriendEventListener
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
     * @param  \App\Events\FamilyFriendEvent  $event
     * @return void
     */
    public function handle(FamilyFriendEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::FAMILY_FRIEND_CREATED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new FamilyFriendCreatedMail($dto->getFamilyfriendName(), $dto->getClientFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_sms){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::familyFriendCreatedMessage($dto->getFamilyfriendName(), $dto->getClientFullname()));
                }
            }else if($dto->getType() == EventType::FAMILY_FRIEND_UPDATED){
                //Send Email
                if (filter_var($event->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                    Mail::to($event->email)->send(new FamilyFriendUpdatedMail($dto->getFamilyfriendName(), $dto->getClientFullname()));
                }

                // Send Sms to user
                if(!empty($event->phone_num) && $send_email){
                    TwilioSMSController::sendSMS($event->phone_num, TwilioSMSController::familyFriendUpdatedMessage($dto->getFamilyfriendName(), $dto->getClientFullname()));
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
