<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    public $table = 'amenities';

    public $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'id'    => 'integer',
        'name'  => 'string',
        'description' => 'string'
    ];

    public static $rules = [
        'name'  => 'required|string|min:2|max:150',
        'description' => 'nullable|string|min:5|max:500'
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_amenity');
    }
}
