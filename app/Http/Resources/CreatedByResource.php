<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreatedByResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource) || is_bool($this->resource)) return [];

        return [
            "id"                => $this->resource->getKey(),
            "full_name"         => $this->resource->name,
            "email"             => $this->resource->email,
            "created_at"	    => $this->resource->created_at->toDateTimeString(),
            "updated_at"	    => $this->resource->updated_at->toDateTimeString(),
        ];
    }
}
