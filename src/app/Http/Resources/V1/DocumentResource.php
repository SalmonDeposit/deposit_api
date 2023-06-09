<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'folder' => $this->folder ? new FolderResource($this->folder) : null,
            'name' => $this->name,
            'type' => $this->type,
            'storageLink' => $this->storage_link,
            'size' => (int) $this->size,
            'createdAt' => (new Carbon($this->created_at))->format('Y-m-d H:i:s'),
            'updatedAt' => (new Carbon($this->updated_at))->format('Y-m-d H:i:s')
        ];
    }
}
