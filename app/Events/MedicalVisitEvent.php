<?php

namespace App\Events;

use App\Dto\MedicalVisitDto;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicalVisitEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dto;
    public $email;
    public $userId;
    public $phoneNumber;
    public $clientId;

    /**
     * @param MedicalVisitDto $dto
     * @param $email
     * @param $userId
     * @param $phoneNumber
     * @param $clientId
     */
    public function __construct(MedicalVisitDto $dto, $email, $userId, $phoneNumber, $clientId)
    {
        $this->dto = $dto;
        $this->email = $email;
        $this->userId = $userId;
        $this->phoneNumber = $phoneNumber;
        $this->clientId =  $clientId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
