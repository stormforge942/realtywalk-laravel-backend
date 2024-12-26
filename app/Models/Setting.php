<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    public $table = 'settings';

    public $timestamps = false;

    public $fillable = [
        'name',
        'value'
    ];

    protected $casts = [
        'name'  => 'string',
        'value' => 'array'
    ];

    public const NOTIFICATION_EMAIL = 'notification_email';
    public const SECRETARY_NOTIFICATION_EMAIL = 'secretary_notification_email';

    public static function getBy($name)
    {
        $setting =  static::whereName($name)->first();

        if (!is_null($setting)) {
            return $setting->value;
        }

        return null;
    }

    public static function getLogo($small = false)
    {
        $public_storage = Storage::disk('Wasabi');
        $dir            = 'site/';
        $logo           = static::getBy('site_logo_expanded') ?: 'no-image';
        $logo_small     = static::getBy('site_logo_collapsed') ?: 'no-image';
        $path           = $dir . ($small ? $logo_small : $logo);

        return $public_storage->exists($path)
            ? $public_storage->url($path) .'?'.time()
            : null;
    }
}
