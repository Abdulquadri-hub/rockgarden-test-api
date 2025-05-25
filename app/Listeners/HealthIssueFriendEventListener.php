<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\HealthIssueFriendEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\HealthIssueFriendMail;
use App\Mail\HealthIssueFriendUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HealthIssueFriendEventListener
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
     * @param  \App\Events\HealthIssueFriendEvent  $event
     * @return void
     */
    public function handle(HealthIssueFriendEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            $friends = UserService::familyFriendUsers($event->clientId);

            if($dto->getType() == EventType::HEALTH_ISSUE_FRIEND){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new HealthIssueFriendMail($dto->getClientFullname(), $dto->getName()));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::healthIssueFriendMessage($dto->getClientFullname(), $dto->getName()));
                        }
                    }
                }
            }else if($dto->getType() == EventType::HEALTH_ISSUE_FRIEND_UPDATED){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new HealthIssueFriendUpdatedMail($dto->getClientFullname(), $dto->getHealthIssueName(), $dto->getName()));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::healthIssueFriendUpdatedMessage($dto->getClientFullname(), $dto->getHealthIssueName(), $dto->getName()));
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
