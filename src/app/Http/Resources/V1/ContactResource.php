<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'lastName' => $this->lastname,
            'askedQuestion' => $this->asked_question,
            'createdAt' => $this->created_at
        ];
    }
}
