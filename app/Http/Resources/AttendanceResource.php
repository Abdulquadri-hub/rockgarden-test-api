<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'staff' => new EmployeeChartResource($this->staff),
            'latitude_checkin' => $this->latitude_checkin,
            'latitude_checkout' => $this->latitude_checkout,
            'longitude_checkin' => $this->longitude_checkin,
            'longitude_checkout' => $this->longitude_checkout,
            'device_checkin' => $this->device_checkin,
            'device_checkout' => $this->device_checkout,
            'time_checkin' => $this->time_checkin,
            'time_checkout' => $this->time_checkout,
            'staff_id' => $this->staff_id,
            'category' => $this->category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
