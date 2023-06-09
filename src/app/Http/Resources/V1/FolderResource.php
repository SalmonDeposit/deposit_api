<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'folder' => $this->folder ? new FolderResource($this->folder) : null,
            'folders' => FolderResource::collection($this->whenLoaded('folders')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'createdAt' => (new Carbon($this->created_at))->format('Y-m-d H:i:s'),
            'updatedAt' => (new Carbon($this->updated_at))->format('Y-m-d H:i:s')
        ];
    }
}
