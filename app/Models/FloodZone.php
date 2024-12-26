<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FloodZone extends Model
{
    public $table = 'flood_zones';

    public static $legends = [
        'A' => '#8d5a99',
        'AE' => '#ff9e17',
        'AH' => '#85b66f',
        'AO' => '#e77148',
        'AR' => '#e5b636',
        'V' => '#d5b43c',
        'VE' => '#b7484b',
        'X' => '#987db7',
        'C' => '#91522d',
        'D' => '#beb297'
    ];

    protected $fillable = [
        'zone_id',
        'flood_zone_ar_id',
        'flood_zone',
        'flood_zone_subtype',
        'area',
        'min_lat',
        'min_lng',
        'max_lat',
        'max_lng',
        'geometry',
        'geometry_json',
        'color',
    ];

    protected $casts = [
        'geometry_json'  => 'object',
    ];

    protected $hidden = ['geometry'];

    /*
     * Expects $bounds to be an array of [minX, minY, maxX, maxY]
     */
    public function scopeIntersectsBounds($query, $bounds)
    {
        $B1 = "{$bounds[1]} {$bounds[0]}";
        $B2 = "{$bounds[3]} {$bounds[0]}";
        $B3 = "{$bounds[3]} {$bounds[2]}";
        $B4 = "{$bounds[1]} {$bounds[2]}";

        return $query->whereRaw("mbrintersects(ST_GeomFromText('POLYGON(($B1, $B2, $B3, $B4, $B1))',4326), geometry)");
    }

    public function updateBounds()
    {
        $minLat = null;
        $minLng = null;
        $maxLat = null;
        $maxLng = null;

        $geometry = self::whereId($this->id)->select([DB::raw('ST_AsGeoJSON(ST_Envelope(geometry)) as geometry')])->first()->geometry ?? null;
        if (!$geometry) {
            Log::debug('No geometry data to calculate bounds with.');
            return;
        }

        $geometry = json_decode($geometry);
        $polys = $geometry->coordinates ?? [];

        $firstPoly = $polys[0] ?? null;
        if ($firstPoly) {
            $minLat = $firstPoly[0][1];
            $minLng = $firstPoly[0][0];
            $maxLat = $firstPoly[0][1];
            $maxLng = $firstPoly[0][0];
        }

        foreach ($polys as $poly) {
            foreach ($poly as $poly2) {
                $minLat = min($poly2[1], $minLat);
                $minLng = min($poly2[0], $minLng);
                $maxLat = max($poly2[1], $maxLat);
                $maxLng = max($poly2[0], $maxLng);
            }
        }

        DB::statement('UPDATE flood_zones set area = ST_Area(geometry)*40075, min_lat = ?, min_lng = ?, max_lat = ?, max_lng = ? where id = ?', [
            $minLat,
            $minLng,
            $maxLat,
            $maxLng,
            $this->id,
        ]);

        $this->refresh();
    }
}
