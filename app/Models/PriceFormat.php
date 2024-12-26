<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Style
 * @package App\Models
 * @version August 03, 2021, 6:13 pm UTC
 *
 * @property string name
 */
class PriceFormat extends Model
{
    const FORMAT_PRICE = 'Price';
    const FORMAT_RANGE = 'Range';
    const FORMAT_TBD = 'TBD';

    public $table = 'price_formats';

    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'    => 'integer',
        'name'  => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name'  => 'required|string|min:1|max:150'
    ];

    /**
     * The properties that belong to the price formaat
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class);
    }

    public function getPriceFormatIDByName($format_name)
    {
        $format_id = null;
        switch ($format_name) {
            case self::FORMAT_PRICE:
                $format_id = $this->where('name', self::FORMAT_TBD)->first()?->id;
                break;
            case self::FORMAT_RANGE:
                $format_id = $this->where('name', self::FORMAT_TBD)->first()?->id;
                break;
            case self::FORMAT_TBD:
                $format_id = $this->where('name', self::FORMAT_TBD)->first()?->id;
                break;
        }

        return $format_id;
    }

    public static function byName(string $name)
    {
        return static::where('name', $name)->first();
    }

    public static function getIDbyName(string $name)
    {
        return static::where('name', $name)->first()?->id;
    }

    public static function getIds(): array
    {
        $priceFormats = static::get();
        $formats['price'] = $priceFormats->firstWhere('name', 'Price')->id;
        $formats['range'] = $priceFormats->firstWhere('name', 'Range')->id;
        $formats['tbd'] = $priceFormats->firstWhere('name', 'TBD')->id;

        return $formats;
    }
}
