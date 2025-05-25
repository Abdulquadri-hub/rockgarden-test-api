<?php

namespace App\Http\Resources;

use App\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'staff_id' => $this->staff_id,
            'client_id' => $this->client_id,
            'staff_present_id' => $this->staff_present_id,
            'staff' => new EmployeeChartResource($this->staff),
            'staff_present' => new UserResource($this->staff_present),
            'client' => new ClientChartResource($this->client),
            'report_date' => $this->report_date,
            'media1' => $this->media1,
            'media2' => $this->media2,
            'media3' => $this->media3,
            'media4' => $this->media4,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
