<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\DocumentClientEvent;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Services\UserService;
use App\Mail\DocumentClientCreatedMail;
use App\Mail\DocumentClientUpdatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DocumentClientEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\DocumentClientEvent  $event
     * @return void
     */
    public function handle(DocumentClientEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            $friends = UserService::familyFriendUsers($event->clientId);

            if($dto->getType() == EventType::DOCUMENT_CLIENT_CREATED){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new DocumentClientCreatedMail($friend->first_name.' '.$friend->last_name, $dto->getClientFullname()));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::documentClientCreatedMessage($friend->first_name.' '.$friend->last_name, $dto->getClientFullname()));
                        }
                    }
                }
            }else if($dto->getType() == EventType::DOCUMENT_CLIENT_UPDATE){
                if(!empty($friends)){
                    foreach ($friends as $friend){
                        //Send Email
                        if (filter_var($friend->email, FILTER_VALIDATE_EMAIL) && $send_email) {
                            Mail::to($friend->email)->send(new DocumentClientUpdatedMail($friend->first_name.' '.$friend->last_name, $dto->getClientFullname()));
                        }

                        // Send Sms to user
                        if(!empty($friend->phone_num) && $send_sms){
                            TwilioSMSController::sendSMS($friend->phone_num, TwilioSMSController::documentClientUpdatedMessage($friend->first_name.' '.$friend->last_name, $dto->getClientFullname()));
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
