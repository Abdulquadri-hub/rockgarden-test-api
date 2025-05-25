<?php

namespace App\Http\Resources;

use App\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthIssueResource extends JsonResource
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
            'review_frequency' => $this->review_frequency,
            'initial_treatment_plan' => $this->initial_treatment_plan,
            'closed_reason' => $this->closed_reason,
            'recorded_by_staff_id' => $this->recorded_by_staff_id,
            'client_id' => $this->client_id,
            'closed_by_user_id' => $this->closed_by_user_id,
            'recorded_by_staff' => new EmployeeResource($this->staff),
            'closed_by_user' => new UserResource($this->closed_user),
            'client' => new ClientResource($this->client),
            'start_date' => $this->start_date,
            'closed_date' => $this->closed_date,
            'image1' => $this->image1,
            'image2' => $this->image2,
            'image3' => $this->image3,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
