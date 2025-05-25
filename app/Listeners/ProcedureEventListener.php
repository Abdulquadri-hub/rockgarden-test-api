<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\ProcedureEvent;
use App\Http\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcedureEventListener
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
     * @param  \App\Events\ProcedureEvent  $event
     * @return void
     */
    public function handle(ProcedureEvent $event)
    {
        $dto = $event->dto;

        try {
            $res = UserService::usersAdmin($dto->getType(), false);

            $send_sms = $res['notification']['sms'];
            $send_email = $res['notification']['email'];

            if($dto->getType() == EventType::PROCEDURE_ADMIN){

            }else if($dto->getType() == EventType::PROCEDURE_FRIEND){

            }
        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
