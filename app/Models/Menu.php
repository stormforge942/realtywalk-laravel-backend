<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'href',
        'icon',
        'slug',
        'parent_id',
        'menu_id',
        'sequence'
    ];

    public function getHrefAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return $value ? url($value) : null;
    }
}
