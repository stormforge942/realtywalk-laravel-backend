<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolygonResult extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'polygon_id',
        'formatted_data',
    ];

    protected $casts = [
        'formatted_data' => 'array',
    ];
}
