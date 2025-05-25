<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'company_name' => $this->company_name,
            'vendor_email' => $this->vendor_email,
            'vendor_address' => $this->vendor_address,
            'vendor_phone' => $this->vendor_phone,
            'vendor_web_site' => $this->vendor_web_site,
            'remarks' => $this->remarks,
            'contact_person' => $this->contact_person,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
