<?php

namespace App\Http\Resources;

use App\Models\Client;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceGroupClientCustomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return new ClientChartResource(Client::where('id', $this->client_id)->first());;
    }
}
