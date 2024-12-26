<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;

class Zone extends Model
{
    use NodeTrait;

    public $table = 'zones';

    public $timestamps = false;

    public $fillable = [
        'name',
        'code',
        'lat',
        'lng',
        'parent_id',
    ];

    public $appends = ['type'];

    protected $casts = [
        'id'   => 'integer',
        'name' => 'string',
        'code' => 'string',
        'lat'  => 'float',
        'lng'  => 'float',
    ];

    public static $rules = [
        'name' => 'required|min:2|max:100',
        'code' => 'nullable|min:2|max:10',
        'lat'  => 'nullable|numeric',
        'lng'  => 'nullable|numeric',
    ];

    public function polygons(): HasMany
    {
        return $this->hasMany(Polygon::class);
    }

    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtolower($value);
    }

    public function getTypeAttribute(): ?string
    {
        if (! is_null($this->depth)) {
            switch ($this->depth) {
                case 0: return 'country';
                case 1: return 'state';
                case 2: return 'city';
                default: return '';
            }
        }

        return '';
    }
}
