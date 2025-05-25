<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemGroupResource extends JsonResource
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
            'group_name' => $this->group_name,
            'taxable' => $this->taxable,
            'type' => $this->type,
            'unit' => $this->unit,
            'images' => $this->images,
            'returnable' => $this->returnable,
            'dimension' => $this->dimension,
            'weight_kg' => $this->weight_kg,
            'manufacturer' => $this->manufacturer,
            'brand' => $this->brand,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
            'currency' => $this->currency,
            'description' => $this->description,
            'items' => $this->items,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
