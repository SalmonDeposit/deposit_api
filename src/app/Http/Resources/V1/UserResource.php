<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'simonCoinStock' => $this->simon_coin_stock,
            'createdAt' => (new Carbon($this->created_at))->format('Y-m-d H:i:s'),
            'updatedAt' => (new Carbon($this->updated_at))->format('Y-m-d H:i:s')
        ];
    }
}
