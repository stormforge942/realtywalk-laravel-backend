<?php

namespace App\Http\Controllers;

use App\Models\SchoolZone;
use App\Repositories\SchoolZoneRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SchoolZoneController extends Controller
{
    protected $schoolZoneRepository;

    public function __construct(SchoolZoneRepository $schoolZoneRepository)
    {
        $this->schoolZoneRepository = $schoolZoneRepository;
    }

    /**
     * List School Zones as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getSchoolZones(Request $request)
    {
        $expire = Carbon::now()->addYear(1);
        $latest_school_date = SchoolZone::latest()->first()->updated_at;
        $cache_key = "schoolzones-{$latest_school_date->toDateString()}-" . $request->type;

        $data = Cache::remember($cache_key, $expire, function () use ($request) {
            return SchoolZone::where('type', $request->type)->select([
                'id',
                'title',
                'title_short',
                'type',
                'geometry_json',
                'color',
            ])->get();
        });

        return response()->json($data);
    }

    /**
     * List School Zone Legends as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getSchoolZoneLegends()
    {
        return response()->json(SchoolZone::$legends);
    }
}
