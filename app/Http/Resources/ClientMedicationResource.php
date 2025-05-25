<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientMedicationResource extends JsonResource
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
            'start_date' => $this->start_date,
            'strength' => $this->strength,
            'is_prn' => $this->is_prn,
            'dosage_morning' => $this->dosage_morning,
            'dosage_morning_when' => $this->dosage_morning_when,
            'dosage_afternoon' => $this->dosage_afternoon,
            'dosage_afternoon_when' => $this->dosage_afternoon_when,
            'dosage_evening' => $this->dosage_evening,
            'dosage_evening_when' => $this->dosage_evening_when,
            'reason_for_medication' => $this->reason_for_medication,
            'other_intake_guide' => $this->other_intake_guide,
            'medication_type' => $this->medication_type,
            'medicine_name' => $this->medicine_name,
            'client_id' => $this->client_id,
            'client' => new ClientChartResource($this->client),
            'created_by_id' => $this->created_by_id,
            'updated_by_id' => $this->updated_by_id,
            'end_date' => $this->end_date,
            'created_by' => new UserChartResource($this->created_by),
            'updated_by' => new UserChartResource($this->updated_by),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
