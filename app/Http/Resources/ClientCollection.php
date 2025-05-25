<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollection extends ResourceCollection
{
    use ResourceHelpers;
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
            return strcmp((empty($a) || empty($a->user)) ? "" : $a->user->first_name, (empty($b) || empty($b->user)) ? "" : $b->user->first_name);
        });

        return [
            'data' => $this->removeNullValues($data),
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }
}
