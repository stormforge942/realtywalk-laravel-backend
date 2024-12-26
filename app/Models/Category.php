<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public $table = 'categories';

    public $fillable = [
        'name'
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];

    public static $rules = [
        'name' => 'required|string|min:2|max:200'
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
