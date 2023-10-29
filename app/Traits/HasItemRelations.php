<?php namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, MorphOne};

trait HasItemRelations
{

    /**
     * @return HasMany
     */
    public function itemhasMany(): HasMany
    {
        return $this->hasMany(item::class);
    }
    /**
     * @return HasOne
     */
    public function itemhasOne(): HasOne
    {
        return $this->hasOne(item::class);
    }
    /**
     * @return BelongsTo
     */
    public function itemBelongsTo(): BelongsTo
    {
        return $this->belongsTo(item::class);
    }

    /**
     * @return MorphOne
     */
    public function itemMorphOne(): MorphOne
    {
        return $this->morphOne(
            item::class,"morphable"
        );
    }

}