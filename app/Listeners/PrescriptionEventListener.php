<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\PrescriptionEvent;
use App\Http\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PrescriptionEventListener
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
     * @param  \App\Events\PrescriptionEvent  $event
     * @return void
     */
    public function handle(PrescriptionEvent $event)
    {
        $dto = $event->dto;



        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::PRESCRIPTION_ADMIN_CREATED){

            }else if($dto->getType() == EventType::PRESCRIPTION_FRIEND_CREATED){

            }else if($dto->getType() == EventType::PRESCRIPTION_ADMIN_UPDATED){

            }else if($dto->getType() == EventType::PRESCRIPTION_FRIEND_UPDATED){

            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
