<?php

namespace App\Http\Controllers;

use App\Models\FloodZone;
use App\Repositories\FloodZoneRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FloodZoneController extends Controller
{
    protected $floodZoneRepository;

    public function __construct(FloodZoneRepository $floodZoneRepository)
    {
        $this->floodZoneRepository = $floodZoneRepository;
    }

    /**
     * List Flood Zones as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function postFloodZones(Request $request)
    {
        if ($request->zoom < 11) {
            return response()->json([]);
        }
        // ini_set('memory_limit', '-1');

        $data = FloodZone::select([
            'id',
            'flood_zone_ar_id',
            'flood_zone',
            'flood_zone_subtype',
            'color',
            DB::raw('ST_AsGeoJSON(geometry) as geometry'),
        ])
            ->whereNotIn('id', $request->exclude)
            ->whereNotNull('geometry')
            ->whereIn('flood_zone', $request->flood_zone)
            ->intersectsBounds($request->bounds)
            ->paginate(250);
        $data->makeVisible(['geometry']);
        $data->setPath('');
        return response()->json($data);
    }

    public function getFloodZones($post_id)
    {
        $cache_key = "floodzones-$post_id";

        if (Cache::has($cache_key)) {
            $data = Cache::has($cache_key) ? Cache::get($cache_key) : [];
            return response()->json($data)->header('Cache-Control', 'max-age=31536000, public');
        }

        return response()->json([]);
    }

    /**
     * List Flood Zone Legends as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getFloodZoneLegends(Request $request)
    {
        $flood_zones = FloodZone::select(['flood_zone'])->groupBy('flood_zone')->get()->pluck('flood_zone')->toArray();

        return response()->json(collect(FloodZone::$legends)->filter(fn ($item, $key) => in_array($key, $flood_zones)))->header('Cache-Control', 'max-age=31536000, public');
    }
}
