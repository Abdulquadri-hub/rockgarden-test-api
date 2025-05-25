<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use ResourceHelpers;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->removeNullValues([
            'id' => $this->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'home_address' => $this->home_address,
            'office_address' => $this->office_address,
            'city' => $this->city,
            'state' => $this->state,
            'state_of_origin' => $this->state_of_origin,
            'phone_num' => $this->phone_num,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'email_verified_at' => $this->email_verified_at,
            'avatar' => $this->avatar,
            'file_img' => $this->file_img,
            'roles' => $this->role,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
