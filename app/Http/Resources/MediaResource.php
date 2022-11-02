<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
            'mime_type' => $this->mime_type,
            'caption' => $this->caption,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
