<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderDetailResource extends JsonResource
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
            'stock_id' => $this->stock_id,
            'order_id' => $this->order_id,
            'item_id' => $this->item_id,
            'item' => $this->item,
            'group_item_id' => $this->group_item_id,
            'group_item' => $this->itemGroup,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'amount' => $this->amount,
            'tax_id' => $this->tax_id,
            'currency' => $this->currency,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
