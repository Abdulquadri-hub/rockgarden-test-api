<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'payment_name' => $this->payment_name,
            'total_amount_paid' => $this->total_amount_paid,
            'payment_amount' => $this->payment_amount,
            'currency' => $this->currency,
            'due_date' => $this->due_date,
            'payment_description' => $this->payment_description,
            'is_monthly_recurrent' => $this->is_monthly_recurrent,
            'next_charge_date' => $this->next_charge_date,
            'invoice_no' => $this->invoice_no,
            'client_id' => $this->client_id,
            'client' => new ClientChartResource($this->client),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
