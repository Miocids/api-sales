<?php namespace App\Traits;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, MorphOne};

trait HasNoteRelations
{

    /**
     * Get the customer that owns the HasNoteRelations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

}