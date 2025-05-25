<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffChartSimpleResource extends JsonResource
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
            'staff_id' => $this->staff_id,
            'client_id' => $this->client_id,
            'comment' => $this->comment,
            'category'  => $this->category,
            'report_date'  => $this->report_date,
            'report_time'  => $this->report_time,
            'staff' => new EmployeeChartResource($this->staff),
            'client' => new ClientChartResource($this->client),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
