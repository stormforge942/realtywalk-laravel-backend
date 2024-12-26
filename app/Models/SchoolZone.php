<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolZone extends Model
{
    public $table = 'school_zones';

    public static $legends = [
        'elementary' => '#8d5a99',
        'highschool' => '#ff9e17',
        'middleschool' => '#85b66f',
    ];

    protected $fillable = [
        'zone_id',
        'title',
        'title_short',
        'type',
        'geometry',
        'geometry_json',
        'color',
    ];

    protected $casts = [
        'geometry_json'  => 'object',
    ];

    protected $hidden = ['geometry'];
}
