<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceGroupResource extends JsonResource
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
            'group_name' => $this->group_name,
            'staffs' => ServiceGroupStaffResource::collection($this->staffGroups),
            'clients' =>  ServiceGroupClientResource::collection($this->clientGroups),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
