<?php

namespace App\Listeners;

use App\Dto\EventType;
use App\Events\SuccessfulRegistrationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SuccessfullRegistrationEventListener
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
     * @param SuccessfulRegistrationEvent $event
     * @return void
     */
    public function handle(SuccessfulRegistrationEvent $event)
    {
        $dto = $event->dto;

        try {

        }catch (\Exception $exception){
            Log::debug($exception->getMessage());
        }
    }
}
