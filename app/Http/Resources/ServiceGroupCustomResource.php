<?php

namespace App\Http\Resources;

use App\Models\ServiceGroupStaff;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceGroupCustomResource extends JsonResource
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
            'staffs' => ServiceGroupStaffCustomResource::collection($this->staffGroups),
            'clients' =>  ServiceGroupClientCustomResource::collection($this->clientGroups),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
