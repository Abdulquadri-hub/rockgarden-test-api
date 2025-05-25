<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FriendFamilyAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $client = new ClientResource($this->client);
        
        return [
            'id' => $this->id,
            'familyfriend_id' => $this->familyfriend_id,
            'client_id' => $this->client_id,
            'family_friend' => new UserResource($this->friend),
            'client' => $client,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
