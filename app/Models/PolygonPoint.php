<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolygonPoint extends Model
{
    public $table = 'polygon_points';

    public $timestamps = false;

    public $fillable = [
        'polygon_id',
        'lat',
        'lng',
        'order',
        'shape_index'
    ];

    protected $casts = [
        'lat' => 'double',
        'lng' => 'double'
    ];

    public function polygon(): BelongsTo
    {
        return $this->belongsTo(Polygon::class);
    }
}
