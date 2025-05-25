<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'order_date' => $this->order_date,
            'invoiced' => $this->invoiced,
            'created_by_user_id' =>  $this->created_by_user_id,
            'created_by_user' =>  new UserChartResource($this->createdByUser),
            'total_amount' => $this->total_amount,
            'client_id' => $this->client_id,
            'client' => new ClientChartResource($this->client),
            'item_id' => $this->item_id,
            'item' => $this->item,
            'item_unit' => $this->item_unit,
            'item_name' => $this->item_name,
            'total_order' => $this->total_order,
            'item_currency' => $this->item_currency,
            'price_per_unit' => $this->price_per_unit,
            'order_details' => $this->order_details,
            'invoice_no' => $this->invoice_no,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
