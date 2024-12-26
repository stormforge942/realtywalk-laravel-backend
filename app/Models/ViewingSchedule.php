<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'user_id',
        'property_id',
        'name',
        'message',
        'phone_number',
        'email'
    ];

    protected $casts = [
        'datetime' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
