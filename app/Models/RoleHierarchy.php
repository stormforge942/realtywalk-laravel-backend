<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHierarchy extends Model
{
    public $timestamps = false;

    protected $table = 'role_hierarchy';

    protected $fillable = [
        'role_id',
        'hierarchy'
    ];
}
