<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Statistic
 * @package App\Models
 * @version March 14, 2020, 8:27 am UTC
 *
 * @property string name
 * @property string descr
 */
class Statistic extends Model
{

    public $table = 'statistics';

    public $fillable = [
        'type_id',
        'name',
        'descr'
    ];

    protected $casts = [
        'id'      => 'integer',
        'type_id' => 'integer',
        'name'    => 'string',
        'descr'   => 'string'
    ];

    public static $rules = [
        'name'  => 'required|max:150',
        'descr' => 'nullable|max:500'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(StatisticType::class);
    }

    public function polygons(): BelongsToMany
    {
        return $this->belongsToMany(Polygon::class, 'polygon_statistic')
                    ->withPivot('value');
    }
}
