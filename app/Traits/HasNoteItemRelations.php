<?php namespace App\Traits;

use App\Models\{ Note, Item };
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, MorphOne};

trait HasNoteItemRelations
{

    /**
     * Get the note that owns the HasNoteItemRelations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_id', 'id');
    }

    /**
     * Get the item that owns the HasNoteItemRelations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function getAttachAttribute(): mixed
    {
        if(isset($this->attributes["attach"]))
            return Storage::disk("public")->url($this->attributes["attach"]);

        return null;
    }

}