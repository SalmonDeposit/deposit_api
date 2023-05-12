<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'shortName' => $this->short_name,
            'longName' => $this->long_name ?? '',
            'salmonCoinCost' => $this->salmon_coin_cost
        ];
    }
}
