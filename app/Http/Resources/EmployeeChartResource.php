<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeChartResource extends JsonResource
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
            'duty_type' => $this->duty_type,
            'user' => new UserChartResource($this->user),
            'designation' => $this->designation,
            'average_rating' => $this->average_rating,
            'total_ratings'  => $this->total_ratings,
        ];
    }
}
