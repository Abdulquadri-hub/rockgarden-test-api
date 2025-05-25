<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'vendor' => $this->vendor,
            'client' => new ClientChartResource($this->client),
            'staff_id' => $this->staff_id,
            'staff' => new EmployeeChartResource($this->staff),
            'order_no' => $this->order_no,
            'reference' => $this->reference,
            'order_date' => $this->order_date,
            'shipment_date' => $this->shipment_date,
            'shipment_preference' => $this->shipment_preference,
            'discount' => $this->discount,
            'status' => $this->status,
            'invoiced' => $this->invoiced,
            'delivery_method' => $this->payment,
            'uploaded_file' => $this->uploaded_file,
            'terms' => $this->terms,
            'notes' => $this->notes,
            'total' => $this->total,
            'adjustment' => $this->adjustment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
