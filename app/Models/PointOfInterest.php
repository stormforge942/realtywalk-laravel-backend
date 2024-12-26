<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PointOfInterest extends Model
{
    public $table = 'points_of_interests';

    protected $fillable = [
        'extId',
        'name',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'state_fips_cd',
        'county_fips_cd',
        'phone',
        'longitude',
        'latitude',
        'polygon_id'
    ];

    public function properties(): HasOne
    {
        return $this->hasOne(Polygon::class);
    }
}
