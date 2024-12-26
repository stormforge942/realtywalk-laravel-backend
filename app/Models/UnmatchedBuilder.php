<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnmatchedBuilder extends Model
{
    protected $table = 'builders_unmatched';

    protected $fillable = [
        'name',
        'builder_id',
        'last_seen'
    ];

    protected $casts = [
        'last_seen' => 'datetime'
    ];

    public function builder(): BelongsTo
    {
        return $this->belongsTo(Builder::class);
    }
}
