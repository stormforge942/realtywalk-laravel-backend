<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyStatus extends Model
{
    public $table = 'property_status';

    public $timestamps = false;

    protected $guarded = [];

    protected $cast =[
        'id' => 'int',
        'name' => 'string'
    ];

    public static function newFromRequest($data)
    {
        return static::create($data);
    }

    public function updateFromRequest($data)
    {
        return $this->update($data);
    }
}
