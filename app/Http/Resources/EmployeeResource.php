<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    use ResourceHelpers;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->removeNullValues([
            'id' => $this->id,
            'duty_type' => $this->duty_type,
            'user' => new UserResource($this->user),
            'employee_no' => $this->employee_no,
            'nationality' => $this->nationality,
            'national_identification_number' => $this->national_identification_number,
            'department' => $this->department,
            'designation' => $this->designation,
            'bank_account_number' => $this->bank_account_number,
            'bank_name' => $this->bank_name,
            'date_employed' => $this->date_employed,
            'average_rating' => $this->average_rating,
            'total_ratings'  => $this->total_ratings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}
