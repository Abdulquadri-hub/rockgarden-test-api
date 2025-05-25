<?php

namespace App\Events;

use App\Dto\PayrunDto;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IndividualPayRunEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dto;
    public $email;
    public $userId;
    public $phoneNumber;

    /**
     * @param PayrunDto $dto
     * @param $email
     * @param $userId
     * @param $phoneNumber
     */
    public function __construct(PayrunDto $dto, $email, $userId, $phoneNumber)
    {
        $this->dto = $dto;
        $this->email = $email;
        $this->userId = $userId;
        $this->phoneNumber = $phoneNumber;
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
