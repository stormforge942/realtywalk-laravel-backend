<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Polygon;
use Carbon\Carbon;

class PolygonCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polygon:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches the initial homepage polygons to speed up the first load.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug('Polygon:cache is running...');
        $fileTime = Carbon::parse('2019-01-01');
        if (Storage::disk('local')->exists(Polygon::CACHE_TREE_FILE)) {
            $fileTime = Carbon::createFromTimestamp(Storage::disk('local')->lastModified(Polygon::CACHE_TREE_FILE));
        }
        $latestPolygonUpdatedAt = Carbon::parse(Polygon::orderByDesc('updated_at')->first()->updated_at ?? Carbon::now()->subYear());
        if ($latestPolygonUpdatedAt->gt($fileTime)) {
            DB::statement("UPDATE polygons set geometry_json = ST_AsGeoJSON(geometry)");

            $this->updateListTreeCache();
        }

        $fileTime = Carbon::parse('2019-01-01');
        if (Storage::disk('local')->exists(Polygon::CACHE_ZOOM1_FILE)) {
            $fileTime = Carbon::createFromTimestamp(Storage::disk('local')->lastModified(Polygon::CACHE_ZOOM1_FILE));
        }
        $latestZoom1PolygonUpdatedAt = Carbon::parse(Polygon::whereZoom(1)->orderByDesc('updated_at')->first()->updated_at ?? Carbon::now()->subYear());
        if ($latestZoom1PolygonUpdatedAt->gt($fileTime)) {
            $this->updateLevel1Cache();
        }

        $deltaTime = microtime(true) - LARAVEL_START;
        if ($deltaTime > 10 * 60) {
            Log::error('Caching took over 10 minutes, increase the kernel schedule interval from everyTenMinutes() to exceed the cache runtime', ['time_seconds' => $deltaTime]);
        }
        return 0;
    }

    private function updateListTreeCache()
    {
        $data = Polygon::select(['id', 'parent_id', '_lft', '_rgt', 'code', 'zoom', 'color', 'slug', DB::raw('title as text')])
            ->get()
            ->toTree()
            ->map(function ($poly) {
                $poly->data = ['id' => $poly->id];
                return $poly;
            })
            ->toJson();

        Storage::disk('local')->put(Polygon::CACHE_TREE_FILE, $data);
    }

    private function updateLevel1Cache()
    {
        $data = Polygon::where('zoom', 1)
            ->select(['id', 'parent_id', 'title', 'color', 'zoom', DB::raw('geometry_json as geometry')])
            ->whereNotNull('geometry')->get()->makeVisible(['geometry'])->toJson();

        Storage::disk('local')->put(Polygon::CACHE_ZOOM1_FILE, $data);
    }
}
