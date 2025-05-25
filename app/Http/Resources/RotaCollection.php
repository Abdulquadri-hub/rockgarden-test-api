<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RotaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->collection->filter(function ($value) {
            return $value != null;
        })->all();

        usort($data, function($a, $b) {
            return strcmp((empty($a->staff) || empty($a->staff->user)) ? "" : $a->staff->user->first_name, (empty($b->staff) || empty($b->staff->user)) ? "" : $b->staff->user->first_name);
        });

        return [
            'data' => $data,
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }
}
