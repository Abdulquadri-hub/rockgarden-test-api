<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentStaffResource extends JsonResource
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
            'doc_title' => $this->doc_title,
            'doc_desc' => $this->doc_desc,
            'file_url' => $this->file_url,
            'staff_id' => $this->staff_id,
            'staff' => new EmployeeChartResource($this->staff),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
