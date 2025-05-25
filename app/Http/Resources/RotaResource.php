<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RotaResource extends JsonResource
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
            'rota_date' =>  $this->rota_date,
            'staff_id' => $this->shift_id,
            'is_present' => $this->is_present,
            'staff' => new EmployeeResource($this->staff),
            'shift' => $this->shift,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
