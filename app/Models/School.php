<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    public $table = 'schools';

    protected $fillable = [
        'extId',
        'state_school_id',
        'school_district_id',
        'name',
        'address',
        'phone_number',
        'school_url',
        'latitude',
        'longitude',
        'polygon_id',
        'school_zone_id'
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(SchoolDistrict::class, 'school_district_id', 'ext_id');
    }
}
