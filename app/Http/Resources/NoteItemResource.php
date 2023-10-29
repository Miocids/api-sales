<?php namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NoteItemResource extends JsonResource
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
            "quantity"      => $this->resource->quantity,
            "total"         => $this->resource->total,
            "total_usd"     => $this->resource->total_usd,
            "total_eur"     => $this->resource->total_eur,
            "attach"        => $this->resource->attach,
            "note"          => new NoteResource($this->resource->note),
            "item"          => new ItemResource($this->resource->item),
            "created_by"	=> new CreatedByResource($this->resource->createdBy),
            "created_at"	=> $this->resource->created_at->toDateTimeString(),
            "updated_at"	=> $this->resource->updated_at->toDateTimeString(),
        ];
    }
}