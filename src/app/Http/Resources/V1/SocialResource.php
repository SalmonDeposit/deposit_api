<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialResource extends JsonResource
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
            'userId' => $this->user_id,
            'subscriberId' => $this->subscriber_id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'avatarUrl' => $this->avatar_url
        ];
    }
}
