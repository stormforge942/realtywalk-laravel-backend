<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateBuilderRequest;
use App\Http\Requests\UpdateBuilderRequest;
use App\Repositories\BuilderRepository;
use App\Jobs\Uploads\BuilderUploadFiles;
use Illuminate\Support\Facades\DB;
use App\Models\Builder;
use App\Models\Polygon;
use App\Models\SEOUrl;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Response;
use Spatie\MediaLibrary\Models\Media;

class BuilderController extends AppBaseController
{
    public function __construct(
        protected BuilderRepository $builderRepository
    ) {}

    /**
     * List Builder as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable(Request $request)
    {
        $limit = $request->get('limit', 10);
        $skip = $request->get('start', 0);
        $columnSearch = $request->get('column', []);
        $order = $request->get('order', null);

        $leftJoinQuery = '(
            SELECT b2.id, b2.alias_of_builder_id, COUNT(p.id) AS pcount
            FROM builders b2
            LEFT JOIN properties p ON b2.id = p.builder_id
            GROUP BY b2.id
        )';

        $result = DB::table('builders')
            ->leftJoin(DB::raw("{$leftJoinQuery} as awp"), 'builders.id', '=', 'awp.alias_of_builder_id')
            ->leftJoin(DB::raw("{$leftJoinQuery} as bwp"), 'builders.id', '=', 'bwp.id')
            ->select(
                'builders.id',
                'builders.name',
                'builders.updated_at',
                DB::raw('COUNT(awp.id) as aliases_count'),
                DB::raw('IFNULL(SUM(awp.pcount), 0) + IFNULL(SUM(bwp.pcount), 0) as properties_count')
            );

        if (!filter_var($request->aliased, FILTER_VALIDATE_BOOLEAN)) {
            $result->whereNull('builders.alias_of_builder_id');
        }

        if ($columnSearch) {
            foreach ($columnSearch as $search) {
                if (!$search['search'])
                    continue;

                $result = $result->having($search['name'], 'LIKE', '%' . addslashes($search['search']) . '%');
            }
        }

        $total = $result->count();

        $result->skip($skip)
            ->limit($limit)
            ->orderBy(
                ($order ? $order['column'] : 'builders.created_at'),
                ($order ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'DESC')
            )
            ->groupBy('builders.id');

        $data = $result->get();
        $ids = $data->pluck('id')->all();
        $urls = SEOUrl::query()
            ->where('entity_type', get_class(new Builder))
            ->whereIn('id', $ids)
            ->get();

        $data = $data->map(function ($item) use ($urls) {
            $item->seourl = $urls->firstWhere('entity_id', $item->id);
            return $item;
        });

        return $this->respond(compact('data', 'total'));
    }

    /**
     * List Unmatched Builder as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTableUnmatchedBuilders(Request $request)
    {
        $limit = $request->get('limit', 10);
        $skip = $request->get('start', 0);
        $columnSearch = $request->get('column', []);
        $order = $request->get('order', null);
        $recount = false;

        $query = DB::table('builders_unmatched')
            ->leftJoin('builders', 'builders.id', '=', 'builders_unmatched.builder_id')
            ->select(
                'builders_unmatched.id',
                'builders_unmatched.name',
                'builders_unmatched.updated_at',
                'builders.id as builder_id',
                DB::raw('IFNULL(builders.name, "–") as builder_name')
            );

        $total = (clone $query)->get()->count();

        if ($columnSearch) {
            foreach ($columnSearch as $search) {
                if (!$search['search']) {
                    continue;
                }

                $query->having($search['name'], 'LIKE', '%' . addslashes($search['search']) . '%');
                $recount = true;
            }
        }

        if ($recount) {
            $total = (clone $query)->get()->count();
        }

        $data = $query->skip($skip)
            ->limit($limit)
            ->orderBy(
                ($order ? $order['column'] : 'builders_unmatched.created_at'),
                ($order ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'DESC')
            )->get()
            ->map(function ($item) {
                $item->show_view_btn = false;
                $item->show_edit_btn = true;
                $item->show_delete_btn = false;

                return $item;
            });

        return $this->respond(compact('data', 'total'));
    }

    /**
     * List Area as json resource in tree data
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTreeArea(Request $request)
    {
        if (!$request->query('lazyload')) {
            if ($request->query('type') == 'zone') {
                return Zone::select('id', 'name as label', 'parent_id')->get();
            }

            if ($request->query('type') == 'polygon') {
                return $this->getPolygons(null, false, $request->query('page'), $request->query('per_page'), false);
            }
        }

        if (!($zone_id = $request->query('zone_id'))) {
            return $this->getZones($request->query('no_disable', false));
        }

        return $this->getPolygons($zone_id, $request->query('parent_id'), $request->query('page'), $request->query('per_page'));
    }

    /**
     * Show builders in front end
     *
     * @return
     */
    public function showFrontend()
    {
        return view('builders.frontend');
    }

    /**
     * Get builders with properties
     *
     * @return  json
     */
    public function getListBuilder(Request $request)
    {
        $page = $request->input("page", 1);
        $data = Builder::getBuilders($request, $page);

        return $this->respond($data);
    }

    /**
     * Search builders with keywords
     *
     * @return  json
     */
    public function searchBuilder(Request $request, $keyword)
    {
        $page = $request->input("page", 1);
        $data = Builder::getBuilders($request, $page, 15, $keyword);

        return $this->respond($data);
    }

    /**
     * Search builders with Text keywords
     *
     * @return  json
     */
    public function searchBuilderText(Request $request, $keyword)
    {
        $page = $request->input("page", 1);
        $data = Builder::getBuilders($request, $page, 15, $keyword, true);

        return $this->respond($data);
    }

    /**
     * List builder as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getList(Request $request)
    {
        if ($id = $request->query('id')) {
            $item = Builder::findOrFail($id);

            return [
                ['id' => $item->id, 'label' => $item->name]
            ];
        }

        $query = Builder::query();

        if ($q = $request->query('q')) {
            $query->where('name', 'like', '%' . $q . '%');
            $query->take(30);
            $query->orderBy('name');
        }

        return $query->get()->map(function ($item) {
            return [
                'id'    => $item->id,
                'label' => $item->name
            ];
        });
    }

    /**
     * Display a listing of the Property.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $showAliased = filter_var($request->aliased, FILTER_VALIDATE_BOOLEAN);
        $hideBlacklist = filter_var($request->hide_blacklist, FILTER_VALIDATE_BOOLEAN);

        return view('builders.index', compact('showAliased', 'hideBlacklist'));
    }

    public function getData(Request $request)
    {
        $user = $request->user();
        $builder = $user->builder;
        $builder['builder_areas'] = $builder->polygons->pluck('title')->all();
        $builder['gallery'] = [];
        $builder['logo'] = null;
        $gallery_urls = [];

        foreach ($builder->getMedia('builders')->all() as $index => $item) {
            $gallery_urls[] = $item->getFullUrl();
        }

        $builder['gallery'] = $gallery_urls;

        if ($logos = $builder->getMedia('builder_logo')->all()) {
            $builder['logo'] = $logos[0]->getFullUrl();
        }

        unset($builder['media']);

        return response()->json($builder);
    }

    /**
     * Show the form for creating a new Builder.
     *
     * @return Response
     */
    public function create()
    {
        return view('builders.create');
    }

    public function fetchBuilderImages(Request $request)
    {
        $builder = Builder::find($request->builderId);
        $gallery = $builder->getMedia('builders');
        foreach ($gallery as $media) {
            $media->fullUrl = $media->getFullUrl();
        }
        return response()->json($gallery->sortBy('order_column')->values()->all());
    }

    public function fetchBuilderLogo(Request $request)
    {
        $builder = Builder::find($request->builderId);
        $logo = $builder->getMedia('builder_logo');
        foreach ($logo as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        return $logo;
    }

    public function sortBuilderImages(Request $request)
    {
        Media::setNewOrder($request->order);

        return response()->json($request->all());
    }

    public function deleteBuilderImages(Request $request)
    {
        $media = Media::find($request->key);
        $property = Builder::find($media->model_id);
        $property->deleteMedia($media->id);

        return response()->json();
    }

    /**
     * Show the Single Builder.
     *
     * @param  string $slug
     * @return Json response
     */
    public function showSingleBuilder($slug)
    {
        $builder = Builder::whereSlug($slug)->firstOrFail();

        if ($builder->alias_of_builder_id) {
            $builder = Builder::findOrFail($builder->alias_of_builder_id);
        }

        $builder->load('properties.polygon', 'properties.builder', 'properties', 'aliasOf.properties');
        $builder->media->filter(function (Media $media) {
            return in_array($media->collection_name, ['builders', 'builder_logo']);
        });
        $builderPrimaryLogo = $builder->getPrimaryLogo();
        $properties = $builder->properties->map(function ($property) {
            $property['is_favorited'] = $property->favorited();
            $property->getFirstMedia();

            return $property;
        });
        $propertiesAliases = collect();

        if ($builder->alias_of) {
            $builder->alias_of->map(function ($builderAlias) {
                $builderAlias->properties->map(function ($property) {
                    $property['is_favorited'] = $property->favorited();
                    $property->getFirstMedia();

                    return $property;
                });
            });
        }

        foreach ($builder->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        foreach ($properties as $property) {
            foreach ($property->media as $media) {
                $media->fullUrl = $media->getFullUrl();
            }

            $property->builderPrimaryLogo = $builderPrimaryLogo;
        }

        $builder['properties'] = $properties->concat($propertiesAliases);

        return $this->respond($builder);
    }

    /**
     * Store a newly created Builder in storage.
     *
     * @param CreateBuilderRequest $request
     *
     * @return Response
     */
    public function store(CreateBuilderRequest $request)
    {
        $input = $request->all();

        try {
            DB::beginTransaction();

            if (empty($input['slug'])) {
                $input['slug'] = Builder::generateSlug($input['name']);
            }

            $builder = $this->builderRepository->create($input);
            $tmpDirectory = (string) Str::orderedUuid();
            $tmpDirectory = "tmp/$tmpDirectory";

            $logos = $request->file('logo', []);
            $gallery = $request->file('gallery', []);

            foreach ($logos as $item) {
                $filename = Str::random(16) . '.' . $item->extension();
                Storage::put("$tmpDirectory/logo/$filename", $item->get());
            }

            # Attach new gallery items to property
            foreach ($gallery as $item) {
                $filename = Str::random(16) . '.' . $item->extension();
                Storage::put("$tmpDirectory/gallery/$filename", $item->get());
            }

            BuilderUploadFiles::dispatch($builder, $tmpDirectory);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'builderId' => $builder->id
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            if (isset($tmpDirectory) && Storage::exists($tmpDirectory)) {
                Storage::deleteDirectory($tmpDirectory);
            }

            Flash::error('An error occured: ' . $e->getMessage())->important();
            throw $e;
        }
    }

    /**
     * Display the specified Builder.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $builder = Builder::withCount('aliases')->findOrFail($id);

        return view('builders.show')->with('builder', $builder);
    }

    public function aliases($builder, Request $request)
    {
        $limit = $request->get('limit', 10);
        $skip = $request->get('start', 0);
        $columnSearch = $request->get('column', []);
        $order = $request->get('order', null);
        $total = Builder::where('alias_of_builder_id', $builder)->count();

        $builder = Builder::where('alias_of_builder_id', $builder)->skip($skip)->limit($limit)
            ->orderBy(
                ($order ? $order['column'] : 'builders.created_at'),
                ($order ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'DESC')
            );

        if ($columnSearch) {
            foreach ($columnSearch as $search) {
                if (!$search['search']) continue;

                $builder->having($search['name'], 'LIKE', '%' . addslashes($search['search']) . '%');
            }

            $data = $builder->get();
            $total = $data->count();
        } else {
            $data = $builder->get();
        }

        return $this->respond(compact('data', 'total'));
    }

    /**
     * Show the form for editing the specified Builder.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $builder = Builder::select('*', DB::raw('SOUNDEX(name) as name_sound'))->with('polygons')->findOrFail($id);

        $gallery = array();
        foreach ($builder->getMedia('builders') as $image) {
            $obj = ["source" => $image->getUrl(), 'id' => $image->id];
            $gallery[] = $obj;
        }

        $logo = array();
        foreach ($builder->getMedia('builder_logo') as $image) {
            $obj = ["source" => $image->getUrl(), 'id' => $image->id];
            $logo[] = $obj;
        }

        $similars = Builder::where('id', '!=', $builder->id)->whereRaw('SOUNDEX(name) like ?', [$builder->name_sound])->get();

        $uploaded_files_session = "uploaded_files_builder_{$builder->id}";
        $uploaded_notice = session()->get($uploaded_files_session);
        if (session()->exists($uploaded_files_session)) {
            session()->forget($uploaded_files_session);
        }

        return view('builders.edit', [
            'gallery'         => $gallery,
            'logo'            => $logo,
            'builder'         => $builder,
            'similars'        => $similars,
            'uploaded_notice' => $uploaded_notice,
        ]);
    }

    public function similarToQuery(Request $request)
    {
        $search = $request->search;

        if ($id = $request->input('builder_id')) {
            return Builder::where('id', $id)->withCount('aliases')->get();
        }

        return Builder::query()
            ->where('id', '!=', $request->exclude_builder_id)
            ->withCount('aliases')
            ->where(function ($query) use ($search) {
                $query->whereRaw('SOUNDEX(name) like SOUNDEX(?)', [$search])
                    ->orWhereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%']);
            })
            ->limit(200)
            ->get();
    }

    /**
     * Update the specified Builder in storage.
     *
     * @param  int                  $id
     * @param  UpdateBuilderRequest $request
     *
     */
    public function update($id, UpdateBuilderRequest $request)
    {
        $builder = $this->builderRepository->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($alias_id = $request->alias_of_builder_id) {
                if (Builder::where(['alias_of_builder_id' => $id, 'id' => $alias_id])->exists()) {
                    return $this->respondWithError('Can not alias two builders with each other');
                }
            } else {
                $request->merge(['alias_of_builder_id' => null]);
            }

            if ($request->input('slug') && $request->input('slug') != $builder->slug) {
                $slug = Builder::generateSlug($request->input('slug'), 50, $builder->id);
                $request->merge(['slug' => $slug]);
            } else if (!trim($request->input('slug'))) {
                $slug = Builder::generateSlug($request->input('name'), 50, $builder->id);
                $request->merge(['slug' => $slug]);
            }

            $builder = $this->builderRepository->update($request->all(), $id);

            if (!$builder->is_uploading_files) {
                $tmpDirectory = (string) Str::orderedUuid();
                $tmpDirectory = "tmp/$tmpDirectory";
                $gallery = $request->file('gallery') ?: [];
                $logos = $request->file('logo') ?: [];
                $deleted = [
                    'logo' => $request->get('deletedLogos', []),
                    'gallery' => $request->get('deletedFiles', []),
                ];
                $sortOrder = array_flip($request->get('sortOrder', []));
                $newFiles = [];

                foreach ($logos as $logo) {
                    $filename = Str::random(16) . '.' . $logo->extension();
                    Storage::put("$tmpDirectory/logo/$filename", $logo->get());
                }

                # Attach new gallery items to property
                foreach ($gallery as $item) {
                    $filename = Str::random(16) . '.' . $item->extension();
                    Storage::put("$tmpDirectory/gallery/$filename", $item->get());
                    $newFiles[$filename] = $item->getClientOriginalName();
                }

                BuilderUploadFiles::dispatch($builder, $tmpDirectory, $deleted, $sortOrder, $newFiles);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'builderId' => $builder->id
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            if (!$builder->is_uploading_files && isset($tmpDirectory) && Storage::exists($tmpDirectory)) {
                Storage::deleteDirectory($tmpDirectory);
            }

            Flash::error('An error occured: ' . $e->getMessage())->important();
            throw $e;
        }
    }

    public function updateAsBuilder(Request $request)
    {
        $user = $request->user();
        $validator = $this->storeAsBuilderValidations($request);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $builder = Builder::find($user->builder_id);
        $inputs = $request->only($builder->fillable);
        $builder = $this->builderRepository->update($inputs, $builder->id);

        if ($request->builder_areas && Polygon::whereIn('id', $request->builder_areas)->count()) {
            $builder->polygons()->sync($request->builder_areas);
        }

        $builder->clearMediaCollection('builder_logo');
        if ($logo = $request->updated_logo) {
            $builder->addMedia($logo->path())
                ->usingFileName(Str::random(16) . '.' . $logo->extension())
                ->toMediaCollection('builder_logo', 'Wasabi');
        }

        $builder->clearMediaCollection('builders');
        if ($gallery = $request->updated_gallery) {
            foreach ($gallery as $item) {
                $builder->addMedia($item->path())
                    ->usingFileName(Str::random(16) . '.' . $item->extension())
                    ->toMediaCollection('builders', 'Wasabi');
            }
        }

        return response()->json([
            'message' => 'Your builder profile has successfully updated.',
            'data'    => $builder,
        ]);
    }

    private function storeAsBuilderValidations(Request $request)
    {
        return Validator::make($request->all(), [
            'name'              => 'required|max:50',
            'email'             => 'required|email|max:128',
            'phone'             => 'required|max:12',
            'website'           => 'nullable|url|max:128',
            'builder_areas'     => 'required',
            'descr'             => 'nullable|max:1000',
            'city'              => 'required|max:128',
            'address1'          => 'nullable|max:128',
            'address2'          => 'nullable|max:128',
            'address3'          => 'nullable|max:128',
            'updated_logo'      => 'mimes:jpeg,bmp,png,webp|max:10000',
            'updated_gallery.*' => 'mimes:jpeg,bmp,png,webp|max:10000',
        ]);
    }

    /**
     * Remove the specified Builder from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $builder = $this->builderRepository->findOrFail($id);

        try {
            DB::beginTransaction();

            $builder->properties()->update(['builder_id' => null]);
            $this->builderRepository->delete($id);

            $builder->clearMediaCollection('builder_logo');
            $builder->clearMediaCollection('builders');

            DB::commit();

            return $this->respond(['message' => '<strong>' . $builder->title . '</strong> has been successfully deleted.']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getZones($noDisable = false)
    {
        $zones = Zone::withDepth()->get()->toTree();

        return $this->getNestedZones($zones, $noDisable);
    }

    public function getNestedZones($zones, $noDisable = false)
    {
        $data = [];

        foreach ($zones as $index => $zone) {
            $data[$index]['id']         = $zone->id;
            $data[$index]['label']      = $zone->name;
            $data[$index]['zone_id']    = $zone->id;
            $data[$index]['isPolygon']  = false;

            if (!$noDisable) {
                $data[$index]['isDisabled'] = true;
            }

            if ($zone->children->count() > 0) {
                $data[$index]['children'] = $this->getNestedZones($zone->children, $noDisable);
            } else {
                $data[$index]['children'] = null;
            }
        }

        return $data;
    }

    public function getPolygons($zone_id = null, $parent_id = null, $page = null, $per_page = null, $with_descendant_count = true)
    {
        $query = Polygon::query();

        $query->select(['id', 'parent_id', 'zone_id', '_lft', '_rgt', 'title as label']);
        $query->without('seourl');

        if ($zone_id) {
            $query->whereZoneId($zone_id);
        }

        if ($with_descendant_count) {
            $query->withCount('descendants');
        }

        if ($parent_id) {
            $query->whereParentId($parent_id);
        } else {
            if (is_null($parent_id)) {
                $query->whereNull('parent_id');
            }
        }

        if ($page) {
            $per_page = $per_page ?: 10000;
            $offset = ($page - 1) * $per_page;

            $query->offset($offset)->limit($per_page);
        }

        $results = $query->get();

        if ($with_descendant_count) {
            return $results->map(function ($item) {
                $item->isPolygon = true;

                if ($item->descendants_count > 0) {
                    $item->children = null;
                }

                return $item;
            });
        }

        return $results;
    }

    public function getNestedAreas($nodes)
    {
        $data = [];

        foreach ($nodes as $index => $node) {
            $data[$index]['id']    = $node->id;
            $data[$index]['label'] = $node->name;

            if ($node->children->count() > 0) {
                $data[$index]['children'] = $this->getNestedAreas($node->children);
            } else {
                $data[$index]['children'] = Polygon::select(['id', 'parent_id', 'zone_id', '_lft', '_rgt', 'title as label'])->whereZoneId($node->id)->with('children')->without('seourl')->get()->toTree();
            }
        }

        return $data;
    }
}
