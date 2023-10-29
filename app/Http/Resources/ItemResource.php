<?php namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (is_null($this->resource) || is_bool($this->resource)) return [];

        return [
            "id"            => $this->resource->getKey(),
            "name"          => $this->resource->name,
            "sku"           => $this->resource->sku,
            "price"         => $this->resource->price,
            "status"        => $this->resource->status,
            "created_by"	=> new CreatedByResource($this->resource->createdBy),
            "created_at"	=> $this->resource->created_at->toDateTimeString(),
            "updated_at"	=> $this->resource->updated_at->toDateTimeString(),
        ];
    }
}