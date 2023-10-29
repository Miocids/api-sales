<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            "id"            => $this->resource->getKey(),
            "date"          => $this->resource->date,
            "total"         => $this->resource->total,
            "customer"      => new CustomerResource($this->resource->customer),
            "created_by"	=> new CreatedByResource($this->resource->createdBy),
            "created_at"	=> $this->resource->created_at->toDateTimeString(),
            "updated_at"	=> $this->resource->updated_at->toDateTimeString(),
        ];
    }
}
