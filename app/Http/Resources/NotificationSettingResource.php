<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingResource extends JsonResource
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
            'trigger_name' => $this->trigger_name,
            'send_sms'=>$this->send_sms,
            'send_email'=>$this->send_email,
            'send_inapp'=>$this->send_inapp,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'system_contact'=>$this->systemContacts,
        ];
    }
}
