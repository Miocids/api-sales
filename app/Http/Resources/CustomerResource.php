<?php namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            "email"         => $this->resource->email,
            "address"       => $this->resource->address,
            "status"        => $this->resource->status,
            "created_by"	=> new CreatedByResource($this->resource->createdBy),
            "created_at"	=> $this->resource->created_at->toDateTimeString(),
            "updated_at"	=> $this->resource->updated_at->toDateTimeString(),
        ];
    }
}