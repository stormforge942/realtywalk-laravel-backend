<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreatePolygonRequest;
use App\Http\Requests\UpdatePolygonRequest;
use App\Jobs\FixPolygonTreeJob;
use App\Jobs\Uploads\PolygonUploadFiles;
use App\Models\PointOfInterest;
use App\Models\Polygon;
use App\Models\PolygonResult;
use App\Models\Property;
use App\Models\School;
use App\Models\SchoolDistrict;
use App\Models\SEOUrl;
use App\Models\StatisticType;
use App\Repositories\PolygonRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Response;
use Spatie\MediaLibrary\Models\Media;

class PolygonController extends AppBaseController
{
    public function __construct(
        protected PolygonRepository $polygonRepository
    ) {}

    /**
     * List Polygon as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable(Request $request)
    {
        $limit = $request->get('limit', 10);
        $skip = $request->get('start', 0);
        $columnSearch = $request->get('column', []);
        $order = $request->get('order', null);
        $total = Polygon::count();

        $polygon = Polygon::leftJoin('zones', 'zones.id', 'polygons.zone_id')
            ->select(
                'polygons.id',
                'polygons.title',
                'polygons.zoom',
                'polygons.zone_id',
                DB::raw('IF(polygons.zone_id = 1, true, false) AS disable_edit_btn'),
                DB::raw('IF(polygons.zone_id = 1, true, false) AS disable_delete_btn'),
                DB::raw('true AS is_polygon'),
                'zones.name as zone_name',
            )
            ->orderBy(
                ($order ? $order['column'] : 'polygons.id'),
                ($order ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'DESC')
            );

        if ($columnSearch) {
            foreach ($columnSearch as $search) {
                if (!$search['search']) continue;
                $polygon->having($search['name'], 'LIKE', '%' . addslashes($search['search']) . '%');
            }

            $total = $polygon->get()->count();
        }

        $data = $polygon->skip($skip)->limit($limit)->get();

        return $this->respond(compact('data', 'total'));
    }

    public function deletePolygonImages(Request $request)
    {
        $media = Media::find($request->key);
        $polygon = Polygon::find($media->model_id);
        $polygon->deleteMedia($media->id);

        return response()->json();
    }

    public function sortPolygonImages(Request $request)
    {
        Media::setNewOrder($request->order);

        return response()->json($request->all());
    }

    public function fetchPolygonImages(Request $request)
    {
        $polygon = Polygon::find($request->polygonId);
        foreach ($polygon->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        return response()->json($polygon->media->sortBy('order_column')->values()->all());
    }

    /**
     * List Polygons as json resource in tree data
     *
     * @param Request $request
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTree(Request $request)
    {
        $zone_id = $request->query('zone_id');

        if (!$zone_id) {
            return $this->respondWithError('Need zone id to load data.');
        }

        $nodes = Polygon::select(['id', 'title as label', 'parent_id', '_lft', '_rgt'])->whereZoneId($zone_id)->where('zoom', '<', 3)
            ->get()->toTree();

        return $nodes;
    }

    /**
     * Display a listing of the Polygon.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view('polygons.index');
    }

    /**
     * Show the form for creating a new Polygon.
     *
     * @return Response
     */
    public function create()
    {
        $statisticTypes = StatisticType::with('statistics')->get();
        $options = [
            'create' => true,
            'edit' =>  false
        ];

        return view('polygons.create', compact('statisticTypes'))->with('options', $options);
    }


    /**
     * Display the specified Polygon.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $polygon = $this->polygonRepository->findOrFail($id);

        $statisticTypes = StatisticType::whereHas('statistics')->get()->map(function ($item) use ($polygon) {
            $statistic_ids = $item->statistics->pluck('id')->all();
            $pol_statistics = $polygon->statistics;

            if ($pol_statistics->count()) {
                $pol_statistics_ids = $pol_statistics->pluck('id')->all();

                $item->has_statistic = is_part_of($statistic_ids, $pol_statistics_ids, true);

                $item->statistics = $item->statistics->map(function ($stat) use ($pol_statistics) {
                    if ($pol_statistic = $pol_statistics->firstWhere('id', $stat->id)) {
                        $stat->value = $pol_statistic->pivot->value;
                    }
                    return $stat;
                });
            }

            return $item;
        });

        if (empty($polygon)) {
            Flash::error('Polygon not found')->important();

            return redirect(route('polygons.index'));
        }

        if ($polygon->id == 1) {
            Flash::error('Polygon is unable to show')->important();

            return redirect(route('polygons.index'));
        }

        return view('polygons.show', compact('polygon', 'statisticTypes'));
    }

    /**
     * Store a newly created Polygon in storage.
     *
     * @param CreatePolygonRequest $request
     *
     * @return Response
     */
    public function store(CreatePolygonRequest $request)
    {
        $this->validate($request, ['code' => 'unique:polygons,code']);

        try {
            DB::beginTransaction();

            $input = $request->except('statistics');
            $input['code'] = generateCodeForPolygon($request->zone_id, $request->parent_id);
            $input['geometry'] = DB::raw("ST_GeomFromGeoJSON('" . $request->input('geoJson') . "')");
            $input['display_as_background'] = !empty($input['display_as_background']);
            $input['links'] = Polygon::fetchLinksDetails($request->input('links'));

            $polygon = $this->polygonRepository->create($input);
            $polygon->updateQuietly(['slug' => Str::slug($polygon->title)]);
            $polygon->updateBounds();
            $polygon->touch();

            if ($polygon->zoom == 2 && $polygon->parent) {
                $polygon->updateQuietly(['color' => $polygon->parent->color]);
            }

            if (is_null($polygon->parent_id)) {
                $polygon->assignParent();
            }

            $statistics = $this->getStatisticRecords($request->input('statistics'));
            if ($statistics) {
                $polygon->statistics()->attach($statistics);
            }

            $tmpDirectory = (string) Str::orderedUuid();
            $tmpDirectory = "tmp/$tmpDirectory";
            $featured_image = $request->file('featured_image');
            $gallery = $request->file('gallery', []);

            if ($featured_image || $gallery) {
                if (isset($featured_image[0])) {
                    $filename = Str::random(16) . '.' . $featured_image[0]->extension();
                    Storage::put("$tmpDirectory/featured_image/$filename", $featured_image[0]->get());
                }

                foreach ($gallery as $item) {
                    $filename = Str::random(16) . '.' . $item->extension();
                    Storage::put("$tmpDirectory/gallery/$filename", $item->get());
                }

                PolygonUploadFiles::dispatch($polygon, $tmpDirectory);
            } else {
                $polygon->is_uploading_files = false;
                $polygon->saveQuietly();
            }

            DB::commit();

            Flash::success('Polygon saved successfully.')->important();
        } catch (Exception $e) {
            DB::rollBack();

            if (isset($tmpDirectory) && Storage::exists($tmpDirectory)) {
                Storage::deleteDirectory($tmpDirectory);
            }

            Flash::error('An error occured: ' . $e->getMessage())->important();

            throw $e;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Show the form for editing the specified Polygon.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $polygon = Polygon::select(['*', DB::raw('ST_AsGeoJSON(geometry) as geometry')])->with('statistics')->whereId($id)->firstOrFail();
        $statisticTypes = StatisticType::with('statistics')->get();

        if (!$polygon) {
            Flash::error('Polygon not found')->important();

            return redirect(route('polygons.index'));
        }

        $polygon->setVisible(['geometry']);

        $options = [
            "create"    => false,
            "edit"      => true,
            "links"     => unserialize($polygon->links),
            "link_list" => $polygon->link_list,
        ];

        $gallery = [];
        foreach ($polygon->getMedia('polygons') as $image) {
            $obj = ["source" => $image->getUrl(), 'id' => $image->id];
            $gallery[] = $obj;
        }

        $featuredImage = ($fiModel = $polygon->getFirstMedia('featured_image'))
            ? ['source' => $fiModel->getUrl(), 'id' => $fiModel->id]
            : null;

        $uploaded_files_session = "uploaded_files_polygon_{$polygon->id}";
        $uploaded_notice = session()->get($uploaded_files_session);
        if (session()->exists($uploaded_files_session)) {
            session()->forget($uploaded_files_session);
        }

        return view('polygons.edit', [
            'polygon'         => $polygon,
            'featured_image'  => $featuredImage,
            'statisticTypes'  => $statisticTypes,
            'statistics'      => $polygon->statistics,
            'gallery'         => $gallery,
            'uploaded_notice' => $uploaded_notice,
            'options'         => $options
        ]);
    }

    /**
     * Update the specified Polygon in storage.
     *
     * @param int $id
     * @param UpdatePolygonRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePolygonRequest $request)
    {
        $polygon = Polygon::with('statistics')->findOrFail($id);

        try {
            DB::beginTransaction();

            $request["display_as_background"] = !empty($request["display_as_background"]);
            $request['links'] = Polygon::fetchLinksDetails($request->input('links'));

            $polygon = $this->polygonRepository->update($request->all(), $id);

            if ($polygon->zoom == 2 && $polygon->parent) {
                $polygon->color = $polygon->parent->color;
            }

            if ($polygon->zoom == 1) {
                $polygon->descendants()->where('zoom', 2)->update([
                    'color' => $polygon->color
                ]);
            }

            $geoJson = $request->input('geoJson');

            if ($geoJson && !empty($geoJson)) {
                $polygon->geometry = DB::raw("ST_GeomFromGeoJSON('$geoJson')");
            }

            $polygon->slug = Str::slug($polygon->title);
            $polygon->save();
            $polygon->updateBounds();

            if (is_null($polygon->parent_id)) {
                $polygon->assignParent();
            }

            if ($statistics = $this->getStatisticRecords($request->input('statistics'))) {
                $polygon->statistics()->sync($statistics);
            }

            if (!$polygon->is_uploading_files) {
                $featured_image = $request->file('featured_image', []);
                $deleted   = [
                    'featured_images' => $request->get('deletedFeaturedImages', []),
                    'gallery' => $request->get('deletedFiles', [])
                ];
                $gallery      = $request->file('gallery') ?: [];
                $sortOrder    = array_flip($request->get('sortOrder', []));
                $tmpDirectory = (string) Str::orderedUuid();
                $tmpDirectory = "tmp/$tmpDirectory";
                $newFiles     = [];

                if ($featured_image) {
                    $filename = Str::random(16) . '.' . $featured_image[0]->extension();
                    Storage::put("$tmpDirectory/featured_image/$filename", $featured_image[0]->get());
                }

                foreach ($gallery as $item) {
                    $filename = Str::random(16) . '.' . $item->extension();
                    Storage::put("$tmpDirectory/gallery/$filename", $item->get());
                    $newFiles[$filename] = $item->getClientOriginalName();
                }

                if ($featured_image || $gallery) {
                    PolygonUploadFiles::dispatch($polygon, $tmpDirectory, $deleted, $sortOrder, $newFiles);
                } else {
                    $polygon->is_uploading_files = false;
                    $polygon->saveQuietly();
                }
            }

            DB::commit();
            Flash::success('Polygon updated successfully.')->important();
        } catch (Exception $e) {
            DB::rollBack();

            if (!$polygon->is_uploading_files && isset($tmpDirectory) && Storage::exists($tmpDirectory)) {
                Storage::deleteDirectory($tmpDirectory);
            }

            Flash::error('An error occured: ' . $e->getMessage())->important();

            throw $e;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified Polygon from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $polygon = $this->polygonRepository->findOrFail($id);
            if (is_null($polygon)) {
                return $this->respondWithError('Polygon not found.');
            }
            if ($polygon->id == 1) {
                return $this->respondWithError('Polygon is prohibited to be deleted.');
            }

            $parent = null;

            if ($polygon->parent_id) {
                $parent = $this->polygonRepository->find($polygon->parent_id);
            }

            // Attach to parent if exists
            $polygon->properties()->update([
                'polygon_id' => $polygon->parent_id ?? null
            ]);

            $polygon->children()->update([
                'parent_id' => $polygon->parent_id ?? null,
                'zoom' => $polygon->zoom > 1 ? ($polygon->zoom - 1) : $polygon->zoom
            ]);

            $polygon->update([
                '_lft' => $parent->_lft ?? 1,
                '_rgt' => $parent->_rgt ?? 1
            ]);

            Polygon::where('id', '=', $id)->delete();
            FixPolygonTreeJob::dispatch();
            // Ensure cache is invalidated
            $lastPoly = Polygon::orderByDesc('id')->first();
            $lastPoly->touch();

            Media::query()
                ->where('model_type', 'App\Models\Polygon')
                ->where('model_id', $polygon->id)
                ->whereIn('collection_name', ['polygons', 'featured_image'])
                ->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Flash::error('Polygon delete error: ' . $e->getMessage())->important();

            throw $e;
        }

        return $this->respond(['message' => '<strong>' . $polygon->title . '</strong> has been successfully deleted.']);
    }

    public function showSingleNeighborhood($slug)
    {
        $slug = rtrim($slug, '/');
        if ($url = SEOUrl::wherePath("/neighborhood/{$slug}")->first()) {
            $polygon = Polygon::select([
                '*', DB::raw('ST_AsGeoJSON(geometry) as geometry')
            ])->with('ancestors', 'zone', 'media')->findOrFail($url->entity_id);
        } else {
            $polygon = Polygon::select([
                '*', DB::raw('ST_AsGeoJSON(geometry) as geometry')
            ])->where('slug', $slug)->with('ancestors', 'zone', 'media')->firstOrFail();
        }

        $polygon->makeVisible('geometry');

        foreach ($polygon->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        $statisticTypes = StatisticType::whereHas('statistics')->get()->map(function ($item) use ($polygon) {
            $statistic_ids = $item->statistics->pluck('id')->all();
            $pol_statistics = $polygon->statistics;

            if ($pol_statistics->count()) {
                $pol_statistics_ids = $pol_statistics->pluck('id')->all();
                $item->has_statistic = is_part_of($statistic_ids, $pol_statistics_ids, true);
                $item->statistics = $item->statistics->map(function ($stat) use ($pol_statistics) {
                    if ($pol_statistic = $pol_statistics->firstWhere('id', $stat->id)) {
                        $stat->value = $pol_statistic->pivot->value;
                    }
                    return $stat;
                });
            }

            return $item;
        });

        $polygon['statisticTypes'] = $statisticTypes;
        $polygon['links'] = unserialize($polygon['links']);

        if ($polygon->zoom == 3) {
            $polygon['all_links'] = array_merge($polygon?->parent?->link_list ?: [], $polygon->link_list);
        } else if (($polygon->zoom == 1 || $polygon->zoom == 2) && empty($polygon->link_list)) {
            $polygon['all_links'] = $polygon->child_links;
        } else {
            $polygon['all_links'] = $polygon->link_list;
        }

        if ($polygon->zoom == 1) {
            $polygon['descendants'] = $polygon->descendants()->select([
                'id',
                'parent_id',
                '_lft',
                '_rgt',
                'slug',
                'title as text'
            ])->get()->toTree();
        }

        $polygon->getFirstMedia('thumb');

        foreach ($polygon->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        return $polygon;
    }

    public function getPolygonCoordinates($polygonId)
    {
        $polygon = Polygon::select([
            'min_lat', 'min_lng', 'max_lat', 'max_lng', 'geometry_json'
        ])->findOrFail($polygonId);

        $polygon->makeHidden(['seourl', 'page_url', 'path_url']);

        $polygon->geo_json = $polygon->geometry_json ? json_decode($polygon->geometry_json) : null;

        return $this->respond($polygon);
    }

    public function getPolygonSchools($polygonId)
    {
        $polygon = Polygon::findOrFail($polygonId);
        $target = $polygon;
        if ($polygon->zoom == 3) {
            $target = $polygon->parent ?? $polygon;
        }
        $polygonSchools = School::whereIn("polygon_id", $target->descendants()->pluck('id')->push($target->id))->get();
        $districts = SchoolDistrict::whereIn("ext_id", $polygonSchools->pluck('school_district_id'))->get();

        return $this->respond(['schools' => $polygonSchools, 'districts' => $districts]);
    }

    public function getPolygonProperties(Request $request, $polygonSlug)
    {
        $polygon = Polygon::where('slug', $polygonSlug)->firstOrFail();
        $allPolygonsId = (new Polygon())->getChildren($polygon);

        $uniqueLatestPropsQuery = Property::query()
            ->select(DB::raw('MAX(id) as id'))
            ->whereIn('polygon_id', $allPolygonsId)
            ->whereIn('status', ['Active', 'Pending', 'Completed', 'To Be Completed', 'Under Construction'])
            ->groupBy('address_number', 'address_street', 'zipcode', 'unit_number');

        $data = Property::query()
            ->select('properties.*')
            ->joinSub($uniqueLatestPropsQuery, 'props', 'props.id', '=', 'properties.id')
            ->whereIn('properties.polygon_id', $allPolygonsId)
            ->whereIn('properties.status', ['Active', 'Pending', 'Completed', 'To Be Completed', 'Under Construction'])
            ->with('builder', 'media')
            ->paginate(20);

        foreach ($data as $property) {
            foreach ($property->media as $media) {
                $media->fullUrl = $media->getFullUrl();
            }
        }

        return $this->respond($data);
    }

    public function getPolygonPointsOfInterest($polygonId)
    {
        $polygonPointOfInterest =  PointOfInterest::where("polygon_id", $polygonId)->get();
        return $this->respond(['data' => $polygonPointOfInterest]);
    }

    public function getStatisticRecords($data)
    {
        if (!$data) {
            return $data;
        }

        foreach ($data as $id => $item) {
            if (is_null($item['value'])) {
                unset($data[$id]);
            }
        }

        return $data;
    }

    public function searchNeighborhood(Request $request, $keyword)
    {
        $query = Polygon::query()->select(['id', 'slug', 'title', 'zoom'])->orderBy('title');

        if ($keyword == 'numeric') {
            $query->whereRaw("title regexp '^[0-9]+'");
        } else {
            $query->where('title', 'like', "$keyword%");
        }

        if (($page = $request->query('page', 1)) !== '-1') {
            $total = $query->count();
            $per_page = $request->query('per_page', 15);
            $query->skip($per_page * ($page - 1))->take($per_page);
        }

        $data = $query->get()->map(function ($item) {
            $descendants = $item->descendants?->pluck('id')->all() ?: [];
            $item->properties = Property::getFromPolygons($item, $descendants)->get();
            $item->properties_pagination = [
                'total' => $item->properties->count(),
                'page'  => 1
            ];
            return $item;
        })->all();

        if ($page === '-1') {
            return $this->respond($data);
        }

        $results = new LengthAwarePaginator($data, $total, $per_page, $page);
        $results->appends($request->query())->setPath(url()->current());

        return $results;
    }

    public function searchNeighborhoodText(Request $request, $keyword, $flatresponse = null)
    {
        while (strpos($keyword, ' ') !== false) {
            $keyword = str_replace(' ', '%', $keyword);
        }

        $response = [];
        $query = Polygon::query()->select(['id','slug','title','zoom','max_lat','min_lat','max_lng','min_lng'])
            ->orderBy('title')
            ->where('title', 'like', "%$keyword%");

        if ($flatresponse === 'true') {
            $query->with('properties', 'ancestors');
            $query->take(25);

            $response = $query->take(25)->get()->map(function ($item) {
                $item->breadcrumbs = implode(' &rsaquo; ', $item->ancestors->pluck('title')->all());
                unset($item->ancestors);
                return $item;
            });

            return $this->respond($response);
        }

        if (($page = $request->query('page', 1)) !== '-1') {
            $total = $query->count();
            $per_page = $request->query('per_page', 15);
            $query->skip($per_page * ($page - 1))->take($per_page);
        }

        $query->orderBy('title');

        $data = $query->get()->map(function ($item) {
            $descendants = $item->descendants?->pluck('id')->all() ?: [];
            unset($item->descendants);
            $item->properties = Property::getFromPolygons($item, $descendants)->get();
            $item->properties_pagination = [
                'total' => $item->properties->count(),
                'page'  => 1
            ];
            return $item;
        })->all();

        if ($page === '-1') {
            return $this->respond($data);
        }

        $results = new LengthAwarePaginator($data, $total, $per_page, $page);
        $results->appends($request->query())->setPath(url()->current());

        return $results;
    }

    public function getHomeGridList(Request $request)
    {
        $per_page = $request->query('per_page', 15);
        $page = $request->query('page', 1);
        $skip = ($page * $per_page) - $per_page;

        $query = Polygon::query()->where('zoom', 1);
        $total = $query->count();

        $query->with('media')
            ->select(['id', 'title'])
            ->orderBy('title')
            ->skip($skip)
            ->take($per_page);

        $polygons = $query->get()
            ->map(function ($item) {
                $item->link_list = $item->link_list;
                $item->featured_image_url = $item->featured_image_url;

                return $item;
            })->all();

        $results = new LengthAwarePaginator($polygons, $total, $per_page, $page);
        $results->appends($request->query())->setPath(url()->current());

        return $results;

    }

    public function getNeighborList(Request $request)
    {
        $query = Polygon::query()->select(['id', 'slug', 'title', 'zoom'])->with('media');

        if ($request->query('zoom')) {
            $query->where('zoom', 1);
        }

        if (($page = $request->query('page', 1)) !== '-1') {
            $total = $query->count();
            $per_page = $request->query('per_page', 15);
            $query->skip($per_page * ($page - 1))->take($per_page);
        }

        $query->orderBy('title');

        $data = $query->get()->map(function ($item) {
            $descendants = $item->descendants?->pluck('id')->all() ?: [];
            $item->properties = Property::getFromPolygons($item, $descendants)->get();
            $item->properties_pagination = [
                'total' => $item->properties->count(),
                'page'  => 1
            ];
            return $item;
        })->all();

        if ($page === '-1') {
            return $this->respond($data);
        }

        $results = new LengthAwarePaginator($data, $total, $per_page, $page);
        $results->appends($request->query())->setPath(url()->current());

        return $results;
    }

    public function list()
    {
        if (!Storage::disk('local')->exists(Polygon::CACHE_TREE_FILE)) {
            return abort(500, 'Polygon list not available, missing cache error');
        }
        return response(Storage::disk('local')->get(Polygon::CACHE_TREE_FILE));
    }

    public function listViewportPoints(Request $request)
    {
        if ($request->forceIds ?? false) {
            return response()->json(
                Polygon::whereIn('id', $request->forceIds)
                    ->select(['id', 'parent_id', 'title', 'color', 'zoom', 'geometry', 'display_as_background'])
                    ->whereNotNull('geometry')
                    ->get()
                    ->makeVisible(['geometry'])
            );
        }

        $file = Polygon::CACHE_ZOOM1_FILE;
        $useCaching = $request->zoom == 1 && count($request->excludeList) == 0;
        if ($useCaching) {
            if (!Storage::disk('local')->exists($file)) {
                return abort(500, 'Polygons unavailable');
            }

            return response(Storage::disk('local')->get($file));
        }

        return DB::transaction(function () use ($request) {
            $query = Polygon::query();

            if ($zoom = $request->zoom) {
                $query->where('zoom', $zoom);
            }

            if ($exclude_list = $request->excludeList) {
                $query->whereNotIn('id', $exclude_list);
            }

            $query->select(['id', 'parent_id', 'title', 'color', 'area', 'zoom', 'display_as_background', DB::raw('geometry_json as geometry')]);
            $query->whereNotNull('geometry_json');

            $query->when($zoom > 1 && !$request->has('disableCulling') && $request->bounds, function ($query) use ($request) {
                $query->intersectsBounds($request->bounds);
            });

            return $query->get()->makeVisible(['geometry'])->toJson();
        });
    }

    public function getPolygonTrunk(Request $request, $polyId)
    {
        $trunk = Polygon::whereDescendantOrSelf($polyId)->pluck('id');
        $ancestors = Polygon::whereAncestorOf($polyId)->pluck('id');

        return response()->json([
            'id' => $polyId,
            'ids' => $trunk,
            'ancestors' => $ancestors,
        ]);
    }

    public function getPolygonList(Request $request)
    {
        $ids = $request->input('ids');
        $results = PolygonResult::query()->whereIn('polygon_id', $ids)->pluck('formatted_data');

        return response()->json($results);
    }

    public function getPolygonListV2(Request $request)
    {
        if (!($ids = $request->input('ids', []))) {
            return $this->respond([]);
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Gal's query
        $query = "
            SELECT
                JSON_ARRAYAGG(f.jso) as results
            FROM (
                WITH RECURSIVE child_nodes_from_parents AS (
                    SELECT
                        id,
                        title,
                        CAST('' AS VARCHAR(2000)) AS parents,
                        parent_id,
                        0 AS level
                    FROM polygons
                    WHERE id IN ($placeholders)
                    UNION ALL
                    SELECT
                        c.id,
                        c.title,
                        CONCAT_WS(',', NULLIF(parents, ''), c.parent_id), c.parent_id,
                        level + 1
                    FROM
                        child_nodes_from_parents p
                    JOIN polygons c ON c.parent_id = p.id
                    WHERE NOT FIND_IN_SET(parents, c.id)
                ),
                parent_nodes_from_children AS (
                    SELECT
                        IF(c.parent_id IN($placeholders), TRUE, FALSE) AS INCLUDE,
                        c.id,
                        c.parent_id,
                        tree.parents,
                        JSON_OBJECTAGG('Title', c.title) AS js
                    FROM child_nodes_from_parents tree
                    JOIN polygons c USING (id)
                    WHERE
                        level > 0 AND NOT FIND_IN_SET((
                            SELECT
                                GROUP_CONCAT(pr.ids)
                            FROM (
                                WITH RECURSIVE distinct_parents AS (
                                    SELECT
                                        CAST('' AS VARCHAR(2000)) AS ids,
                                        id,
                                        _lft,
                                        _rgt
                                    FROM polygons
                                    WHERE id IN ($placeholders)
                                    UNION
                                    SELECT
                                        p.parent_id,
                                        dp.id,
                                        dp._lft,
                                        dp._rgt
                                    FROM distinct_parents dp
                                    JOIN polygons p ON p._lft > dp._lft AND p._rgt < dp._rgt AND p.parent_id NOT IN($placeholders)
                                )
                                SELECT ids, id, _lft, _rgt FROM distinct_parents WHERE ids != ''
                            )
                        pr), c.id)
                    GROUP BY c.parent_id
                    UNION ALL
                    SELECT
                        IF(FIND_IN_SET('". implode(',', $ids) ."', c.parent_id), TRUE, FALSE) AS INCLUDE,
                        c.id,
                        c.parent_id,
                        bree.parents,
                        JSON_OBJECT(
                            c.id,
                            c.title,
                            'children',
                        js) AS js
                    FROM parent_nodes_from_children bree
                    JOIN polygons c ON c.id = bree.parent_id AND c.id NOT IN($placeholders)
                )
                SELECT
                    JSON_OBJECT(
                        'parent_id',
                        parent_id,
                        'children',
                        JSON_ARRAYAGG(js)
                    ) AS jso
                FROM parent_nodes_from_children
                WHERE INCLUDE = TRUE
                GROUP BY parent_id
            ) f;
        ";

        // Yefta's query
        $query2 = "
            WITH RECURSIVE
                target_ids(id) AS (
                    SELECT CAST(id AS UNSIGNED)
                    FROM JSON_TABLE(
                        ?,
                        '$[*]' COLUMNS (id VARCHAR(255) PATH '$')
                    ) AS jt
                ),
                hierarchy AS (
                    SELECT
                        p.id,
                        p.parent_id,
                        p.title,
                        p._lft,
                        p._rgt,
                        p.zoom,
                        su.path,
                        0 AS level,
                        CAST(p.id AS CHAR(200)) AS path_string
                    FROM polygons p
                    LEFT JOIN seo_urls su ON su.entity_id = p.id AND su.entity_type = 'App\\Models\\Polygon'
                    WHERE p.id IN (SELECT id FROM target_ids)
                    OR EXISTS (
                        SELECT 1 FROM target_ids t
                        WHERE p._lft <= (SELECT _lft FROM polygons WHERE id = t.id)
                        AND p._rgt >= (SELECT _rgt FROM polygons WHERE id = t.id)
                    )

                    UNION ALL

                    SELECT
                        p.id,
                        p.parent_id,
                        p.title,
                        p._lft,
                        p._rgt,
                        p.zoom,
                        su.path,
                        h.level + 1,
                        CONCAT(h.path_string, ',', p.id)
                    FROM polygons p
                    JOIN hierarchy h ON p.parent_id = h.id
                    LEFT JOIN seo_urls su ON su.entity_id = p.id AND su.entity_type = 'App\\Models\\Polygon'
                    WHERE p._lft > h._lft AND p._rgt < h._rgt
                ),
                json_tree AS (
                    SELECT
                        h.id,
                        h.parent_id,
                        JSON_OBJECT(
                            'id', h.id,
                            'text', h.title,
                            'parent_id', h.parent_id,
                            '_lft', h._lft,
                            '_rgt', h._rgt,
                            'zoom', h.zoom,
                            'path', h.path,
                            'children', JSON_ARRAY()
                        ) AS node_json
                    FROM hierarchy h
                ),
                nested_tree AS (
                    SELECT
                        jt1.id,
                        jt1.parent_id,
                        JSON_OBJECT(
                            'id', JSON_EXTRACT(jt1.node_json, '$.id'),
                            'text', JSON_EXTRACT(jt1.node_json, '$.text'),
                            'parent_id', JSON_EXTRACT(jt1.node_json, '$.parent_id'),
                            '_lft', JSON_EXTRACT(jt1.node_json, '$._lft'),
                            '_rgt', JSON_EXTRACT(jt1.node_json, '$._rgt'),
                            'zoom', JSON_EXTRACT(jt1.node_json, '$.zoom'),
                            'path', JSON_EXTRACT(jt1.node_json, '$.path'),
                            'children', IFNULL(
                                JSON_ARRAYAGG(
                                    JSON_REMOVE(jt2.node_json, '$.parent_id')
                                ),
                                JSON_ARRAY()
                            )
                        ) AS node_json
                    FROM json_tree jt1
                    LEFT JOIN json_tree jt2 ON jt1.id = jt2.parent_id
                    GROUP BY jt1.id, jt1.parent_id, jt1.node_json
                )
                SELECT JSON_ARRAYAGG(node_json) AS result
                FROM nested_tree
                WHERE parent_id IS NULL OR id IN (SELECT id FROM target_ids)
                ORDER BY JSON_EXTRACT(node_json, '$._lft');
        ";

        // Asweqwfu***@!%^ query only until level 6
        $query3 = $this->getPolygonListQueryV2($ids);

        // $results = DB::select($query, array_merge($ids, $ids, $ids, $ids, $ids));
        $results = DB::select($query3);

        if (empty($results)) {
            $this->respond([]);
        }

        return $this->respond(json_decode($results[0]->result, 1));
    }

    private function getPolygonListQuery()
    {
        return "
            WITH RECURSIVE

            -- Create target_ids to pass variable number of IDs
            target_ids(id) AS (
                SELECT CAST(id AS UNSIGNED)
                FROM JSON_TABLE (
                    ?,
                    '$[*]' COLUMNS (id VARCHAR(255) PATH '$')
                ) AS jt
            ),

            -- Create CTE to retrieve all relevant polygons based on target ids above
            hierarchy AS (
                SELECT
                    p.id,
                    p.parent_id,
                    p.title,
                    p._lft,
                    p._rgt,
                    p.zoom,
                    su.path,
                    0 AS level
                FROM polygons p
                LEFT JOIN seo_urls su ON su.entity_id = p.id AND su.entity_type = 'App\\Models\\Polygon'
                WHERE p.id IN (SELECT id FROM target_ids)
                OR EXISTS (
                    -- All polygons that are in the target_ids list.
                    SELECT 1 FROM target_ids t
                    JOIN polygons p2 ON p2.id = t.id
                    WHERE p._lft <= p2._lft AND p._rgt >= p2._rgt
                )

                UNION ALL

                -- Recursive part
                SELECT
                    p.id,
                    p.parent_id,
                    p.title,
                    p._lft,
                    p._rgt,
                    p.zoom,
                    su.path,
                    h.level + 1
                FROM polygons p
                JOIN hierarchy h ON p.parent_id = h.id
                LEFT JOIN seo_urls su ON su.entity_id = p.id AND su.entity_type = 'App\\Models\\Polygon'
                WHERE p._lft > h._lft AND p._rgt < h._rgt
            )

            -- The query begin from here: level 1 - 6 only though
            SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', h1.id,
                        'text', h1.title,
                        'parent_id', h1.parent_id,
                        '_lft', h1._lft,
                        '_rgt', h1._rgt,
                        'zoom', h1.zoom,
                        'path', h1.path,
                        'children', IFNULL(
                            (
                                SELECT JSON_ARRAYAGG(
                                    JSON_OBJECT(
                                        'id', h2.id,
                                        'text', h2.title,
                                        'parent_id', h2.parent_id,
                                        '_lft', h2._lft,
                                        '_rgt', h2._rgt,
                                        'zoom', h2.zoom,
                                        'path', h2.path,
                                        'children', IFNULL(
                                            (
                                                SELECT JSON_ARRAYAGG(
                                                    JSON_OBJECT(
                                                        'id', h3.id,
                                                        'text', h3.title,
                                                        'parent_id', h3.parent_id,
                                                        '_lft', h3._lft,
                                                        '_rgt', h3._rgt,
                                                        'zoom', h3.zoom,
                                                        'path', h3.path,
                                                        'children', IFNULL(
                                                            (
                                                                SELECT JSON_ARRAYAGG(
                                                                    JSON_OBJECT(
                                                                        'id', h4.id,
                                                                        'text', h4.title,
                                                                        'parent_id', h4.parent_id,
                                                                        '_lft', h4._lft,
                                                                        '_rgt', h4._rgt,
                                                                        'zoom', h4.zoom,
                                                                        'path', h4.path,
                                                                        'children', IFNULL(
                                                                            (
                                                                                SELECT JSON_ARRAYAGG(
                                                                                    JSON_OBJECT(
                                                                                        'id', h5.id,
                                                                                        'text', h5.title,
                                                                                        'parent_id', h5.parent_id,
                                                                                        '_lft', h5._lft,
                                                                                        '_rgt', h5._rgt,
                                                                                        'zoom', h5.zoom,
                                                                                        'path', h5.path,
                                                                                        'children', IFNULL(
                                                                                            (
                                                                                                SELECT JSON_ARRAYAGG(
                                                                                                    JSON_OBJECT(
                                                                                                        'id', h6.id,
                                                                                                        'text', h6.title,
                                                                                                        'parent_id', h6.parent_id,
                                                                                                        '_lft', h6._lft,
                                                                                                        '_rgt', h6._rgt,
                                                                                                        'zoom', h6.zoom,
                                                                                                        'path', h6.path,
                                                                                                        'children', JSON_ARRAY()
                                                                                                    )
                                                                                                )
                                                                                                FROM hierarchy h6
                                                                                                WHERE h6.parent_id = h5.id
                                                                                                ORDER BY h6._lft
                                                                                            ),
                                                                                            JSON_ARRAY()
                                                                                        )
                                                                                    )
                                                                                )
                                                                                FROM hierarchy h5
                                                                                WHERE h5.parent_id = h4.id
                                                                                ORDER BY h5._lft
                                                                            ),
                                                                            JSON_ARRAY()
                                                                        )
                                                                    )
                                                                )
                                                                FROM hierarchy h4
                                                                WHERE h4.parent_id = h3.id
                                                                ORDER BY h4._lft
                                                            ),
                                                            JSON_ARRAY()
                                                        )
                                                    )
                                                )
                                                FROM hierarchy h3
                                                WHERE h3.parent_id = h2.id
                                                ORDER BY h3._lft
                                            ),
                                            JSON_ARRAY()
                                        )
                                    )
                                )
                                FROM hierarchy h2
                                WHERE h2.parent_id = h1.id
                                ORDER BY h2._lft
                            ),
                            JSON_ARRAY()
                        )
                    )
                ) AS result
            FROM hierarchy h1
            WHERE h1.parent_id IS NULL OR h1.id IN (SELECT id FROM target_ids)
            ORDER BY h1._lft;
        ";
    }

    private function getPolygonListQueryV2(array $ids): string
    {
        $targetIds = array_filter($ids, function ($id) {
            return is_numeric($id) && intval($id) > 0;
        });

        $targetIds = array_map('intval', $targetIds);

        $targetIdsCTE = "target_ids(id) AS (\n    " .
            implode(" UNION ALL\n    ", array_map(function($id) {
                return "SELECT " . $id;
            }, $targetIds)) .
        "\n),";

        return "
            WITH RECURSIVE

            -- Create target_ids to pass variable number of IDs
            $targetIdsCTE

            -- Create CTE to retrieve all relevant polygons based on target ids above
            hierarchy AS (
                SELECT
                    p.id,
                    p.parent_id,
                    p.title,
                    p._lft,
                    p._rgt,
                    p.zoom,
                    su.path,
                    0 AS level
                FROM polygons p
                LEFT JOIN seo_urls su ON su.entity_id = p.id AND su.entity_type = 'App\\\\Models\\\\Polygon'
                WHERE p.id IN (SELECT id FROM target_ids)
                OR EXISTS (
                    -- All polygons that are in the target_ids list.
                    SELECT 1 FROM target_ids t
                    JOIN polygons p2 ON p2.id = t.id
                    WHERE p._lft <= p2._lft AND p._rgt >= p2._rgt
                )

                UNION ALL

                -- Recursive part
                SELECT
                    p.id,
                    p.parent_id,
                    p.title,
                    p._lft,
                    p._rgt,
                    p.zoom,
                    su.path,
                    h.level + 1
                FROM polygons p
                JOIN hierarchy h ON p.parent_id = h.id
                LEFT JOIN seo_urls su ON su.entity_id = p.id AND su.entity_type = 'App\\\\Models\\\\Polygon'
                WHERE p._lft > h._lft AND p._rgt < h._rgt
            )

            -- The query begin from here: level 1 - 6 only though
            SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', h1.id,
                        'text', h1.title,
                        'parent_id', h1.parent_id,
                        '_lft', h1._lft,
                        '_rgt', h1._rgt,
                        'zoom', h1.zoom,
                        'path', h1.path,
                        'children', IFNULL(
                            (
                                SELECT JSON_ARRAYAGG(
                                    JSON_OBJECT(
                                        'id', h2.id,
                                        'text', h2.title,
                                        'parent_id', h2.parent_id,
                                        '_lft', h2._lft,
                                        '_rgt', h2._rgt,
                                        'zoom', h2.zoom,
                                        'path', h2.path,
                                        'children', IFNULL(
                                            (
                                                SELECT JSON_ARRAYAGG(
                                                    JSON_OBJECT(
                                                        'id', h3.id,
                                                        'text', h3.title,
                                                        'parent_id', h3.parent_id,
                                                        '_lft', h3._lft,
                                                        '_rgt', h3._rgt,
                                                        'zoom', h3.zoom,
                                                        'path', h3.path,
                                                        'children', IFNULL(
                                                            (
                                                                SELECT JSON_ARRAYAGG(
                                                                    JSON_OBJECT(
                                                                        'id', h4.id,
                                                                        'text', h4.title,
                                                                        'parent_id', h4.parent_id,
                                                                        '_lft', h4._lft,
                                                                        '_rgt', h4._rgt,
                                                                        'zoom', h4.zoom,
                                                                        'path', h4.path,
                                                                        'children', IFNULL(
                                                                            (
                                                                                SELECT JSON_ARRAYAGG(
                                                                                    JSON_OBJECT(
                                                                                        'id', h5.id,
                                                                                        'text', h5.title,
                                                                                        'parent_id', h5.parent_id,
                                                                                        '_lft', h5._lft,
                                                                                        '_rgt', h5._rgt,
                                                                                        'zoom', h5.zoom,
                                                                                        'path', h5.path,
                                                                                        'children', IFNULL(
                                                                                            (
                                                                                                SELECT JSON_ARRAYAGG(
                                                                                                    JSON_OBJECT(
                                                                                                        'id', h6.id,
                                                                                                        'text', h6.title,
                                                                                                        'parent_id', h6.parent_id,
                                                                                                        '_lft', h6._lft,
                                                                                                        '_rgt', h6._rgt,
                                                                                                        'zoom', h6.zoom,
                                                                                                        'path', h6.path,
                                                                                                        'children', JSON_ARRAY()
                                                                                                    )
                                                                                                )
                                                                                                FROM hierarchy h6
                                                                                                WHERE h6.parent_id = h5.id
                                                                                                ORDER BY h6._lft
                                                                                            ),
                                                                                            JSON_ARRAY()
                                                                                        )
                                                                                    )
                                                                                )
                                                                                FROM hierarchy h5
                                                                                WHERE h5.parent_id = h4.id
                                                                                ORDER BY h5._lft
                                                                            ),
                                                                            JSON_ARRAY()
                                                                        )
                                                                    )
                                                                )
                                                                FROM hierarchy h4
                                                                WHERE h4.parent_id = h3.id
                                                                ORDER BY h4._lft
                                                            ),
                                                            JSON_ARRAY()
                                                        )
                                                    )
                                                )
                                                FROM hierarchy h3
                                                WHERE h3.parent_id = h2.id
                                                ORDER BY h3._lft
                                            ),
                                            JSON_ARRAY()
                                        )
                                    )
                                )
                                FROM hierarchy h2
                                WHERE h2.parent_id = h1.id
                                ORDER BY h2._lft
                            ),
                            JSON_ARRAY()
                        )
                    )
                ) AS result
            FROM hierarchy h1
            WHERE h1.parent_id IS NULL OR h1.id IN (SELECT id FROM target_ids)
            ORDER BY h1._lft;
        ";
    }

    public function getGeometry(Request $request)
    {
        $polygons = Polygon::query()
            ->select(DB::raw('ST_AsText(ST_Union(ST_GeomFromText(concat("GEOMETRYCOLLECTION (",group_concat(ST_AsText(geometry)),")")),ST_GeomFromText(concat("GEOMETRYCOLLECTION (",group_concat(ST_AsText(geometry)),")")))) as geometry_wkt'))
            ->whereIn('id', $request->input('ids'))
            ->get()
            ->each(function ($item) {
                $item->makeHidden(['seourl', 'page_url', 'path_url']);
            });

        $results = [];

        foreach ($polygons as $polygon) {
            if (!$polygon->geometry_wkt) {
                continue;
            }

            $geo = new \geoPHP();
            $mapper = new \Spinen\Geometry\Support\TypeMapper();
            $geometry = new \Spinen\Geometry\Geometry($geo, $mapper);
            $collection = $geometry->parseWkt($polygon->geometry_wkt);

            $results[] = [
                'geometry' => $collection->toJson()
            ];
        }

        return response()->json($results);
    }
}
