<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticType extends Model
{
    public $table = 'statistic_types';

    public $fillable = [
        'name',
        'format',
        'descr'
    ];

    protected $casts = [
        'id'     => 'integer',
        'name'   => 'string',
        'format' => 'string',
        'descr'  => 'string'
    ];

    public static $rules = [
        'name'   => 'required|max:150',
        'format' => 'required',
        'descr'  => 'nullable|max:500'
    ];

    public function statistics(): HasMany
    {
        return $this->hasMany(Statistic::class, 'type_id');
    }
}
