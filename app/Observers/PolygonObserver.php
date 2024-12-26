<?php

namespace App\Observers;

use App\Models\Polygon;

class PolygonObserver
{
    public function saved(Polygon $polygon): void
    {
        $polygon->generateSEOURL();
    }

    public function deleting(Polygon $polygon): void
    {
        $polygon->seourl()->delete();
    }
}
