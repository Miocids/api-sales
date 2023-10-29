<?php namespace App\Traits;

use App\Models\{Company, User};
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

trait HasRelation
{
    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,"created_by","id"
        )->select("id","email","name","created_at","updated_at");
    }

    public function getClientAttribute(): mixed
    {
        if(isset($this->attributes["client"]) && $this->attributes["client"] !== "[]")
            return json_decode($this->attributes["client"],true);

        return null;
    }

    public function getStatusAttribute(): mixed
    {
        if(isset($this->attributes["status"]))
            return !!$this->attributes["status"];

        return false;
    }

}