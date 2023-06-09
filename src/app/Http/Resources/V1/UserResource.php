<?php

declare(strict_types=1);

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
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'profiles' => ProfileResource::collection($this->whenLoaded('profiles')),
            'simonCoinStock' => $this->simon_coin_stock,
            'createdAt' => (new Carbon($this->created_at))->format('Y-m-d H:i:s'),
            'updatedAt' => (new Carbon($this->updated_at))->format('Y-m-d H:i:s'),
            'isAdmin' => $this->is_admin
        ];
    }
}
