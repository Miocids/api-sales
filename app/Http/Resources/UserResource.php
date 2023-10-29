<?php namespace App\Http\Resources;

use App\Services\RoleService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "username"      => $this->resource->username,
            "status"        => $this->resource->status,
            "is_notify"     => !!$this->resource->is_notify,
            "created_at"	=> $this->resource->created_at->toDateTimeString(),
            "updated_at"	=> $this->resource->updated_at->toDateTimeString(),
        ];
    }
}
