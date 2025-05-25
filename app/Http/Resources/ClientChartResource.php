<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'user' => new UserChartResource($this->user),
            'room_location' => $this->room_location,
            'room_number' => $this->room_number,
            'room_suffix' => $this->room_suffix,
            'client_type' => $this->client_type,
        ];
    }
}
