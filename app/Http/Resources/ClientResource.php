<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    use ResourceHelpers;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  $this->removeNullValues([
            'id' => $this->id,
            'category' => $this->category,
            'user' => new UserResource($this->user),
            'client_no' => $this->client_no,
            'marital_status' => $this->marital_status,
            'nationality' => $this->nationality,
            'religious_pref' => $this->religious_pref,
            'after_death_pref' => $this->after_death_pref,
            'sex_of_carer_pref' => $this->sex_of_carer_pref,
            'doctors_surgery' => $this->doctors_surgery,
            'gp' => $this->gp,
            'mental_health_doctor' => $this->mental_health_doctor,
            'funeral_director' => $this->funeral_director,
            'allergies' => $this->allergies,
            'medical_diagnosis' => $this->medical_diagnosis,
            'medical_history' => $this->medical_history,
            'current_illness' => $this->current_illness,
            'dietary_needs' => $this->dietary_needs,
            'treatment_guide' => $this->treatment_guide,
            'treatment_guide_info' => $this->treatment_guide_info,
            'height_cm' => $this->height_cm,
            'eye_colour' => $this->eye_colour,
            'hair_colour' => $this->hair_colour,
            'build' => $this->build,
            'hair_length' => $this->hair_length,
            'eye_length' => $this->eye_length,
            'weight_on_admission_kg' => $this->weight_on_admission_kg,
            'uses_hearing_aid' => $this->uses_hearing_aid,
            'maiden_name' => $this->maiden_name,
            'prev_occupation' => $this->prev_occupation,
            'date_of_arrival' => $this->date_of_arrival,
            'client_type' => $this->client_type,
            'careplan' => $this->careplan,
            'medications' => $this->medications,
            'death_records' => $this->deathRecords,
            'invoices' => $this->invoices,
            'key_contacts' => $this->keyContacts,
            'medical_histories' => $this->medicalHistories,
            'medication_intakes' => $this->medicationInTakes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'room_location' => $this->room_location,
            'room_number' => $this->room_number,
            'room_suffix' => $this->room_suffix,
            'prev_address' => $this->prev_address,
            'postal_code' => $this->postal_code,
            'admitted_from' => $this->admitted_from,
            'admitted_by' => $this->admitted_by
        ]);
    }
}
