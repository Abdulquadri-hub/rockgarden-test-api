<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'unit' => $this->unit,
            'image1' => $this->image1,
            'image2' => $this->image2,
            'image3' => $this->image3,
            'category_name' => $this->category_name,
            'sku' => $this->sku,
            'reorder_level' => $this->reorder_level,
            'vendor_id' => $this->vendor_id,
            'current_stock_level' => $this->current_stock_level,
            'vendor' => new VendorResource($this->vendor),
            'returnable' => $this->returnable,
            'dimension' => $this->dimension,
            'weight_kg' => $this->weight_kg,
            'manufacturer' => $this->manufacturer,
            'brand' => $this->brand,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
            'currency' => $this->currency,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
