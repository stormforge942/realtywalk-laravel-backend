<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Style extends Model
{
    public $table = 'styles';

    public $fillable = [
        'name',
        'descr'
    ];

    protected $casts = [
        'id'    => 'integer',
        'name'  => 'string',
        'descr' => 'string'
    ];

    public static $rules = [
        'name'  => 'required|string|min:2|max:150',
        'descr' => 'nullable|string|min:5|max:500'
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }
}
