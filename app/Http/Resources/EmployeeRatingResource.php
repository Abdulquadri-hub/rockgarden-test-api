<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeRatingResource extends JsonResource
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
            'reviewer_id' => $this->reviewer_id,
            'reviewer_name' => $this->reviewer_name,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'staff_id' => $this->staff_id,
            'client_id' => $this->client_id,
            'staff' => new EmployeeChartResource($this->staff),
            'client' => new ClientChartResource($this->client),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
