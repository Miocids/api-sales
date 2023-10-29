<?php namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, MorphOne};

trait HasCustomerRelations
{

    /**
     * @return HasMany
     */
    public function customerhasMany(): HasMany
    {
        return $this->hasMany(customer::class);
    }
    /**
     * @return HasOne
     */
    public function customerhasOne(): HasOne
    {
        return $this->hasOne(customer::class);
    }
    /**
     * @return BelongsTo
     */
    public function customerBelongsTo(): BelongsTo
    {
        return $this->belongsTo(customer::class);
    }

    /**
     * @return MorphOne
     */
    public function customerMorphOne(): MorphOne
    {
        return $this->morphOne(
            customer::class,"morphable"
        );
    }

}