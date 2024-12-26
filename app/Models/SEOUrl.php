<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SEOUrl extends Model
{
    protected $table = 'seo_urls';

    protected $fillable = ['path'];

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
