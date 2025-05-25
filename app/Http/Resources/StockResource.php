<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'item_id' => $this->item_id,
            'item_name' => $this->item_name,
            'item_category' => $this->item_category,
            'stock_level_before' => $this->stock_level_before,
            'stock_level_after' => $this->stock_level_after,
            'stock_entry' => $this->stock_entry,
            'unit' => $this->unit,
            'item' => $this->item,
            'created_by_user' =>  new UserChartResource($this->createdByUser),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
