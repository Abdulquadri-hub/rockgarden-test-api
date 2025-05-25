<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffAssignmentCollection extends ResourceCollection
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
            return strcmp((empty($a->client) || empty($a->client->user)) ? "" : $a->client->user->first_name, (empty($b->client) || empty($b->client->user)) ? "" : $b->client->user->first_name);
        });

        return [
            'data' => $data,
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }
}
