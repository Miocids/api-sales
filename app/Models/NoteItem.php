<?php

namespace App\Models;

use App\Observers\{ CreatedByObserver };
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\{HasNoteItemRelations, HasRelation};
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoteItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasNoteItemRelations, HasRelation;

    public $guarded = [];
    public array $dates = [
        'deleted_at'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:00',
        'updated_at' => 'datetime:Y-m-d H:00'
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        self::observe([new CreatedByObserver()]);
    }
}
