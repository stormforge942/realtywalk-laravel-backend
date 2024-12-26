<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreatePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Jobs\Uploads\PropertyUploadFiles;
use App\Mail\PropertySchedule;
use App\Models\Amenity;
use App\Models\Builder;
use App\Models\Category;
use App\Models\Polygon;
use App\Models\PriceFormat;
use App\Models\Property;
use App\Models\PropertyFavorite;
use App\Models\PropertyResult;
use App\Models\PropertyStatus;
use App\Models\Setting;
use App\Models\ViewingSchedule;
use App\Repositories\PropertyRepository;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyController extends AppBaseController
{
    public function __construct(
        protected PropertyRepository $propertyRepository
    ) {}

    /**
     * Display a listing of the Property.
     */
    public function index(Request $request)
    {
        return view('properties.index');
    }

    /**
     * List Property as json resource
     *
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function dataTable(Request $request)
    {
        $limit = $request->get('limit', 10);
        $skip = $request->get('start', 0);
        $columnSearch = $request->get('column', []);
        $order = $request->get('order', null);

        if ($order && isset($order['column'])) {
            $replaces = [
                'property_type' => 'properties.type'
            ];
            $order['column'] = strtr($order['column'], $replaces);
        }

        $joinSubQuery = Property::query()->select('id');

        $joinSubQuery->orderBy(
            ($order ? $order['column'] : 'id'),
            ($order ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'DESC')
        );

        if ($columnSearch) {
            foreach ($columnSearch as $search) {
                if (!$search['search']) continue;
                $joinSubQuery->where($search['name'], 'LIKE', '%' . addslashes($search['search']) . '%');
            }
        }

        $total = $joinSubQuery->count();

        $joinSubQuery->skip($skip)->limit($limit);

        $query = Property::query();

        $query->joinSub($joinSubQuery, 'sub', 'sub.id', '=', 'properties.id');
        $query->leftJoin('builders', 'properties.builder_id', 'builders.id');

        $query->select([
            'properties.id',
            'properties.title',
            'properties.builder_id',
            'properties.updated_at',
            'properties.full_address',
            'properties.request_status',
            'properties.type',
            'properties.status',
            'properties.type as property_type',
            DB::raw('IF(ISNULL(builders.name), "-", builders.name) AS builder_name'),
            'properties.path_url',
        ]);

        $query->orderBy(
            ($order ? $order['column'] : 'properties.id'),
            ($order ? ($order['asc'] == 'true' ? 'ASC' : 'DESC') : 'DESC')
        );

        $data = $query->get();

        return $this->respond(compact('data', 'total'));
    }

    /**
     * Display the specified Property.
     */
    public function show($id)
    {
        $property = $this->propertyRepository->find($id);

        if (empty($property)) {
            Flash::error('Property not found')->important();

            return redirect(route('properties.index'));
        }

        return view('properties.show')->with('property', $property);
    }

    /**
     * Show the form for creating a new Property.
     */
    public function create()
    {
        $propertiesStatus = PropertyStatus::orderBy('name', 'asc')->get();
        $priceFormats = PriceFormat::orderBy('id', 'asc')->get();
        $amenities = Amenity::get();
        $gallery = [];

        return view('properties.create', compact('propertiesStatus', 'priceFormats', 'amenities', 'gallery'));
    }

    /**
     * Store a newly created Property in storage.
     */
    public function store(CreatePropertyRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            if ($validated['price_from'] ?? false) {
                $validated['price_from'] = preg_replace('/[,]/', '', $validated['price_from']);
            }
            if ($validated['price_to'] ?? false) {
                $validated['price_to'] = preg_replace('/[,]/', '', $validated['price_to']);
            }
            if ($validated['sqft'] ?? false) {
                $validated['sqft'] = preg_replace('/[.,]/', '', $validated['sqft']);
            }

            if ((int) $validated['price_format_id'] == PriceFormat::getIDbyName('TBD')) {
                $validated['price_from'] = null;
            }

            $validated['created_by'] = $request->user()->id ?? null;

            $fiveYearsAgo = Carbon::now()->subYears(5);
            $yearBuilt = $validated['year_built'] ? Carbon::parse($validated['year_built'] . '-01-01') : null;
            $validated['new_construction'] = ($yearBuilt?->gte($fiveYearsAgo)) || ((($validated['status'] ?? null) === 'Under Construction'));

            // crazy tweak, it should be gone later
            $validated['lot_sqft'] = $validated['lot_sqft'] ?? 0;

            $property = $this->propertyRepository->create($validated);
            $gallery = $request->file('gallery') ?: [];

            if ($styles = $request->input('styles_id')) {
                $property->styles()->attach($styles);
            }

            if ($amenities = $request->input('amenities_id')) {
                $property->amenities()->attach($amenities);
            }

            $property->updateAmenityFilters();

            $gallery = $request->file('gallery') ?: [];
            $tmpDirectory   = (string) Str::orderedUuid();
            $tmpDirectory   = "tmp/$tmpDirectory";

            foreach ($gallery as $item) {
                $filename = Str::random(16) . '.' . $item->extension();
                Storage::put("$tmpDirectory/gallery/$filename", $item->get());
            }

            if ($gallery) {
                PropertyUploadFiles::dispatch($property, $tmpDirectory);
            } else {
                $property->is_uploading_files = false;
                $property->saveQuietly();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            if (isset($tmpDirectory) && Storage::exists($tmpDirectory)) {
                Storage::deleteDirectory($tmpDirectory);
            }

            Flash::error('An error occured: ' . $e->getMessage())->important();

            throw $e;
        }

        return response()->json([
            'status'     => 'success',
            'propertyId' => $property->id
        ]);
    }

    public function storeAsBuilder(Request $request)
    {
        $validator = $this->storeAsBuilderValidations($request);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            DB::beginTransaction();

            $request->request->add([
                'builder_id' => $request->user()->builder_id,
                'type'       => 1
            ]);

            $inputs = $request->except(['gallery']);

            if ($input['price_from'] ?? false) {
                $inputs['price_from'] = preg_replace('/[,]/', '', $inputs['price_from']);
            }

            if ($input['price_to'] ?? false) {
                $inputs['price_to'] = preg_replace('/[,]/', '', $inputs['price_to']);
            }

            if ((int) $inputs['price_format_id'] == PriceFormat::getIDbyName('TBD')) {
                $inputs['price_from'] = null;
            }

            if ($input['sqft'] ?? false) {
                $inputs['sqft'] = preg_replace('/[.,]/', '', $inputs['sqft']);
            }

            // crazy tweak, it should be gone later
            $inputs['lot_sqft'] = $inputs['lot_sqft'] ?? 0;

            $property = $this->propertyRepository->create($inputs);

            if ($styles = $request->input('styles_id')) {
                $property->styles()->attach($styles);
            }

            if ($amenities = $request->input('amenities_id')) {
                $property->amenities()->attach($amenities);
            }

            $property->updateAmenityFilters();

            $gallery = $request->file('gallery') ?: [];
            foreach ($gallery as $item) {
                $property->addMedia($item->path())
                    ->usingFileName(Str::random(16) . '.' . $item->extension())
                    ->toMediaCollection('properties', 'Wasabi');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return response()->json([
            'message' => 'Your property has successfully added.',
            'property' => $property
        ]);
    }

    /**
     * Show the form for editing the specified Property.
     */
    public function edit($id)
    {
        # Get property, check if exists
        $property = Property::with('amenities')->findOrFail($id);
        $property->setVisible(['is_uploading_files']);

        # Status
        $propertiesStatus = PropertyStatus::orderBy('name', 'asc')->get();
        $priceFormats = PriceFormat::orderBy('id', 'asc')->get();
        $status = Setting::getBy('property_status');
        # Amenities
        $amenities = Amenity::get();

        $uploaded_files_session = "uploaded_files_property_{$property->id}";
        $uploaded_notice = session()->get($uploaded_files_session);
        if (session()->exists($uploaded_files_session)) {
            session()->forget($uploaded_files_session);
        }

        $gallery = array();
        foreach ($property->getMedia('properties') as $image) {
            $obj = ["source" => $image->getUrl(), 'id' => $image->id];
            $gallery[] = $obj;
        }

        return view('properties.edit', [
            'property'         => $property,
            'propertiesStatus' => $propertiesStatus,
            'priceFormats'     => $priceFormats,
            'status'           => $status,
            'amenities'        => $amenities,
            'propertiesStatus' => $propertiesStatus,
            'gallery'          => $gallery,
            'uploaded_notice'  => $uploaded_notice
        ]);
    }

    /**
     * Update the specified Property in storage.
     */
    public function update(int $id, UpdatePropertyRequest $request)
    {
        $validated = $request->validated();
        $property = $this->propertyRepository->findOrFail($id);

        try {
            DB::beginTransaction();

            $input          = $request->except(['styles_id', 'amenities_id']);
            $styles         = $request->input('styles_id') ?: [];
            $amenities      = $request->input('amenities_id') ?: [];



            if ($input['price_from'] ?? false) {
                $input['price_from'] = preg_replace('/[,]/', '', $validated['price_from']);
            }

            if ($input['price_to'] ?? false) {
                $input['price_to'] = preg_replace('/[,]/', '', $validated['price_to']);
            }

            if ($input['sqft'] ?? false) {
                $input['sqft'] = preg_replace('/[.,]/', '', $validated['sqft']);
            }

            if ($input['lot_sqft'] ?? false) {
                $input['lot_sqft'] = preg_replace('/[.,]/', '', $validated['lot_sqft']);
            }

            if ((int) $input['price_format_id'] == PriceFormat::getIDbyName('TBD')) {
                $input['price_from'] = null;
            }

            $fiveYearsAgo = Carbon::now()->subYears(5);
            $yearBuilt = $input['year_built'] ? Carbon::parse($input['year_built'] . '-01-01') : null;
            $input['new_construction'] = ($yearBuilt?->gte($fiveYearsAgo)) || ((($input['status'] ?? null) === 'Under Construction'));

            $property = $this->propertyRepository->update($input, $id);

            $property->styles()->sync($styles);
            $property->amenities()->sync($amenities);
            $property->updateAmenityFilters();

            if (!$property->is_uploading_files) {
                $gallery        = $request->file('gallery') ?: [];
                $deletedFiles   = $request->get('deletedFiles', []);
                $tmpDirectory   = (string) Str::orderedUuid();
                $tmpDirectory   = "tmp/$tmpDirectory";
                $sortOrder      = array_flip($request->get('sortOrder', []));
                $newFiles       = [];

                # Attach new gallery items to property
                foreach ($gallery as $item) {
                    $filename = Str::random(16) . '.' . $item->extension();
                    Storage::put("$tmpDirectory/gallery/$filename", $item->get());
                    $newFiles[$filename] = $item->getClientOriginalName();
                }

                if ($gallery) {
                    PropertyUploadFiles::dispatch($property, $tmpDirectory, $deletedFiles, $sortOrder, $newFiles);
                } else {
                    $property->is_uploading_files = false;
                    $property->saveQuietly();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            if (!$property->is_uploading_files && isset($tmpDirectory) && Storage::exists($tmpDirectory)) {
                Storage::deleteDirectory($tmpDirectory);
            }

            Flash::error('An error occured: ' . $e->getMessage())->important();

            throw $e;
        }

        return response()->json(['status' => 'success', 'propertyId' => $property->id]);
    }

    /**
     * Remove the specified Property from storage.
     */
    public function destroy(int $id)
    {
        $property = $this->propertyRepository->findOrFail($id);

        try {
            DB::beginTransaction();

            $this->propertyRepository->delete($id);
            $property->clearMediaCollection('properties');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Flash::error('An error occured: ' . $e->getMessage())->important();

            throw $e;
        }

        return $this->respond(['message' => '<strong>' . $property->title . '</strong> has been successfully deleted.']);
    }

    public function deleteImageUpload(Request $request)
    {
        $media = Media::find($request->input('key'));
        $property = Property::find($media->model_id);
        $property->deleteMedia($media->id);

        return response()->json();
    }

    public function sortPropertyImages(Request $request)
    {
        Media::setNewOrder($request->order);
        return response()->json($request->all());
    }

    public function fetchPropertyImages(Request $request)
    {
        if ($request->has('restore')) {
            $image = Media::where('id', $request->restore)->first();
            return $image->getUrl();
        }

        $property = Property::with(['media' => function ($q) {
            $q->orderBy('order_column');
            $q->orderBy('created_at');
        }])->find($request->propertyId);

        $property->getFirstMediaUrl('thumb');

        foreach ($property->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        return response()->json($property->media);
    }

    /**
     * Show the Single Property.
     *
     * @return view
     */
    public function showSingleProperty(Request $request, $path)
    {
        if (!is_a_number($path)) {
            $path = rtrim($path, '/');
            // $url = SEOUrl::wherePath("/property/{$path}")->firstOrFail();
            $property = Property::where('path_url', "/property/{$path}")->firstOrFail();
            $id = $property->id;
        } else {
            $id = (int) $path;
        }

        $property = Property::with(['media' => function ($q) {
            $q->orderBy('order_column');
            $q->orderBy('created_at');
        }, 'builder.media', 'polygon.ancestors'])->findOrFail($id);
        $ancestors = $property->polygon->ancestors ?? [];
        $neighborhood = $property->polygon ?? null;
        $subdivision = $property->polygon ?? null;

        if (count($ancestors) > 1) {
            $subdivision = $ancestors[0] ?? $property->polygon;
        }

        $property['neighborhood'] = $neighborhood->title ?? 'N/A';
        $property['subdivision'] = $subdivision->title ?? 'N/A';
        $property['neighborhood_slug'] = $neighborhood->slug ?? null;
        $property['neighborhood_path'] = $neighborhood && $neighborhood->seourl ? $neighborhood->seourl->path : null;
        $property['subdivision_slug'] = $subdivision->slug ?? null;
        $property['subdivision_path'] = $subdivision && $subdivision->seourl ? $subdivision->seourl->path : null;
        $property['price_format_name'] = $property->priceFormat->name;
        $property['amenities'] = $property->amenities()->pluck('name')->toArray();
        $property['styles'] = $property->styles()->pluck('name')->toArray();
        $property['category'] = $property->category->name;

        $property->getFirstMedia('thumb');

        foreach ($property->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        foreach ($property->builder->media ?? [] as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        $property['original_request_status'] = $property->getRawOriginal('request_status');

        return $this->respond($property);
    }

    protected function storeAsBuilderValidations(Request $request)
    {
        return Validator::make($request->all(), [
            'title'           => 'required|min:2|max:100',
            'price_format_id' => 'required|integer',
            'price_from'      => 'nullable|regex:/[\d]{2},[\d]{2}/',
            'price_to'        => 'nullable|regex:/[\d]{2},[\d]{2}/',
            'address_number'  => 'nullable|min:3|max:50',
            'address_street'  => 'nullable|min:3|max:250',
            'unit_number'     => 'nullable',
            'zipcode'         => 'required|min:2|string|max:10',
            'lat'             => 'nullable|numeric',
            'lng'             => 'nullable|numeric',
            'category_id'     => 'required',
            'polygon_id'      => 'required',
            'bedrooms'        => 'nullable|integer',
            'bathrooms_full'  => 'nullable|integer',
            'bathrooms_half'  => 'nullable|integer',
            'descr'           => 'nullable|min:10|string',
            'year_built'      => 'nullable|date_format:Y',
            'lot_size'        => 'nullable|numeric',
            'status'          => 'required|string|max:40',
            'sqft'            => 'required|regex:/^\d+(,\d+)*$/',
            'video_embed'     => 'nullable|max:500',
            'gallery.*'       => 'mimes:jpeg,bmp,png,webp|max:10000'
        ]);
    }

    /**
     * Get amenities
     *
     * @param  Request $request
     * @return Response
     */
    public function getAmenities(Request $request)
    {
        $amenities = Amenity::get();

        return response()->json($amenities);
    }

    /**
     * Get price formats
     *
     * @param  Request $request
     * @return Response
     */
    public function getPriceFormats(Request $request)
    {
        $priceFormats = PriceFormat::orderBy('id', 'asc')->get();
        return response()->json($priceFormats);
    }

    /**
     * Favorite a specific property
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function favoriteProperty(Request $request, Property $property)
    {
        if (!($user = Auth::user())) {
            abort(401);
        }

        $inputs = $request->validate(['conn' => 'required']);

        PropertyFavorite::add($user->id, $property->id, $inputs['conn']);

        return $this->respond([
            'favorites' => $user->getSimpleFavoritedProperties()
        ]);
    }

    /**
     * UnFavorite a specific property
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function unFavoriteProperty(Request $request, Property $property)
    {
        if (!($user = Auth::user())) {
            abort(401);
        }

        $inputs = $request->validate(['conn' => 'required']);

        PropertyFavorite::remove($user->id, $property->id, $inputs['conn']);

        return $this->respond([
            'favorites' => $user->getSimpleFavoritedProperties()
        ]);
    }

    public function userFavoritedProperties(Request $request)
    {
        if (!($user = Auth::user())) {
            abort(401);
        }

        $data = $user->getFavoritedProperties();

        return $this->respond($data);
    }

    public function getProperties()
    {
        $data = Property::select(
            'properties.id',
            'properties.polygon_id',
            'properties.title',
            'properties.builder_id',
            'properties.address_number',
            'properties.address_street',
            'properties.unit_number',
            'properties.descr',
            'properties.price_from',
            'properties.price_to',
            'properties.updated_at',
            'properties.request_status',
            'properties.type',
            'properties.status',
            'properties.path_url',
            'properties.image_urls',
        )->with(['polygon' => function ($q) {
            $q->select('polygons.id', 'polygons.title');
        }])->with(['builder' => function ($q) {
            $q->select('builders.id', 'builders.name');
        }])->whereNotIn('properties.status', Property::invalidStatuses)->limit(10)->latest()->get();

        $data = $data->map(function (Property $property) {
            $property['is_favorited'] = $property->favorited();
            $property->getFirstMedia();

            if ($property->builder) {
                $property->builderPrimaryLogo = $property->builder->getPrimaryLogo();
            }

            return $property;
        });

        foreach ($data as $datum) {
            foreach ($datum->media as $media) {
                $media->fullUrl = $media->getFullUrl();
            }
        }

        return $this->respond($data);
    }

    public function filterPropertyBuilders(Request $request)
    {
        $builderQuery = Builder::whereHas('properties', function ($propertyQuery) use ($request) {
            // Filter data as described in request
            $this->applyPropertyFilter($propertyQuery, $request, false);
        })->orderBy('name');

        $aliases = (clone $builderQuery)->whereNotNull('alias_of_builder_id')->get();
        $aliasIds = $aliases->pluck('alias_of_builder_id');
        $builders = $builderQuery->whereNull('alias_of_builder_id')->get();
        $builders->concat(
            Builder::whereIn('id', $aliasIds)
                ->whereNotIn('id', $builders->pluck('id'))
                ->get()
        );
        $builders->sortBy('name');

        // Takes properties and sorts it as described in request
        return response()->json(['builders' => $builders]);
    }

    public function listCommunitiesBuiltIn(Request $request, $builderId)
    {
        $propertyQuery = Property::query();
        $propertyQuery->where('request_status', 'approved');
        $polygon_ids = $propertyQuery->select('id', 'builder_id', 'polygon_id', 'type')
            ->where('builder_id', $builderId)
            ->whereNotNull('polygon_id')
            ->pluck('polygon_id')
            ->toArray();

        $polygons = Polygon::select('id', 'title')
            ->whereIn('id', array_unique($polygon_ids))
            ->orderBy('title', 'ASC')
            ->paginate(500);

        return response()->json(['polygons' => $polygons]);
    }

    /**
     * Filter properties as it described in the request
     */
    public function filterProperties(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $query = PropertyResult::query()->applyFilters($filters);

        if ($filters['formap']) {
            $query->select(['map_data']);

            if ($bounds = $filters['bounds']) {
                $query->whereBetween('lat', [$bounds['min_lat'], $bounds['max_lat']]);
                $query->whereBetween('lng', [$bounds['min_lng'], $bounds['max_lng']]);
            }

            $zoom = $filters['zoom'];

            $take = match (true) {
                $zoom >= 12 || $filters['initial_load'] => 250,
                $zoom >= 8 && $zoom < 12 => 400,
                $zoom >= 5 && $zoom < 8 => 600,
                default => null
            };

            if ($take) {
                $query->take($take);
            }

            $properties = $query->pluck('map_data');

            return response($properties, 200)->header('Content-Type', 'text/plain');
        }

        $properties = $query->sortProperties($request);

        return response($properties, 200)->header('Content-Type', 'text/plain');
    }

    public function streamFilterProperties(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        Property::$withoutAppends = !!$filters['formap'];
        $query = Property::query()->applyFilters($filters);

        if ($filters['formap']) {
            $query->select([
                'map_data',
            ]);

            $properties = $query->pluck('map_data');

            return response($properties, 200)->header('Content-Type', 'text/plain');
        }

        // Takes properties and sorts it as described in request
        return $this->sortProperties($request, $query);
    }

    protected function getFiltersFromRequest(Request $request)
    {
        $only_new_constructions = config('app.only_new_construction_properties');
        $request_only_new_constructions = $request->query('only_new_constructions', true);
        $has_min_max_price = !empty($request->input('minimumPrice')) && !empty($request->input('maximumPrice'));
        $has_min_price = !empty($request->input('minimumPrice')) && empty($request->input('maximumPrice'));
        $has_max_price = !empty($request->input('maximumPrice')) && empty($request->input('minimumPrice'));

        $has_builder_filter = $request->has('buildersId');
        $builder_ids = ($input_builder_ids = $request->input('buildersId'))
            ? (is_array($input_builder_ids) ? $input_builder_ids : [$input_builder_ids])
            : [];

        if ($propertyTypes = $request->input('selectedPropertyTypes')) {
            $names = [];
            foreach ($propertyTypes as $propertyType) {
                $names[] = str_replace('\\', '', $propertyType);
            }
            if (!in_array('any', $names)) {
                $category_ids = Category::whereIn('name', $names)->pluck('id')->all();
            }
        }

        if ($request->input('lotType') == 'builder_sqft') {
            $default_lot_type = 'properties.sqft';
        } else if ($request->input('lotType') == 'acres') {
            $default_lot_type = 'properties.lot_size';
        } else {
            $default_lot_type = 'properties.sqft';
        }

        $map_info = $request->input('map_info');
        $has_filters = $request->hasAny('minimumPrice', 'maximumPrice', 'listingStatus', 'bedroomsMin', 'bathrooms', 'bathroomsHalf', 'garageOptions', 'selectedPropertyTypes', 'lotType', 'storiesMin', 'storiesMax', 'squareFeetMin', 'squareFeetMax', 'hasPool', 'hasElevator');

        return [
            'has_filters'        => $has_filters,
            'new_construction'   => $only_new_constructions && $request_only_new_constructions,
            'formap'             => filter_var($request->query('formap'), FILTER_VALIDATE_BOOL),
            'show_all'           => filter_var($request->query('showAll'), FILTER_VALIDATE_BOOL),
            'min_price'          => $request->input('minimumPrice', 0),
            'max_price'          => $request->input('maximumPrice', 0),
            'has_min_max_price'  => $has_min_max_price,
            'has_min_price'      => $has_min_price,
            'has_max_price'      => $has_max_price,
            'status'             => $request->input('listingStatus'),
            'category_ids'       => $category_ids ?? [],
            'bedrooms_min'       => $request->input('bedroomsMin'),
            'bathrooms_full'     => $request->input('bathrooms'),
            'bathrooms_half'     => $request->input('bathroomsHalf'),
            'garage_capacity'    => $request->input('garageOptions'),
            'default_lot_type'   => $default_lot_type,
            'square_feet_min'    => $request->input('squareFeetMin'),
            'square_feet_max'    => $request->input('squareFeetMax'),
            'stories_min'        => $request->input('storiesMin'),
            'stories_max'        => $request->input('storiesMax'),
            'has_pool'           => filter_var($request->input('hasPool'), FILTER_VALIDATE_BOOL),
            'has_elevator'       => filter_var($request->input('hasElevator'), FILTER_VALIDATE_BOOL),
            'has_builder_filter' => $has_builder_filter,
            'builder_ids'        => $builder_ids,
            'polygon_ids'        => $request->input('polygons'),
            'bounds'             => $map_info['bounds'] ?? null,
            'zoom'               => $map_info['zoom'] ?? null,
            'initial_load'       => $map_info['init'] ?? null
        ];
    }

    public function getPropertyQueryInfo(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $query = Property::query()->applyFilters($filters);
        $total = $query->count('id');
        $per_page = $request->query('per_page', 500);
        $ranges = $total > $per_page ? range(1, $total, $per_page) : [1];

        $query->select(DB::raw('id, ROW_NUMBER() OVER (ORDER BY id ASC) AS rownum'));

        $blocks = DB::query()
            ->from($query, 'p')
            ->select('p.id', 'p.rownum')
            ->whereIn('p.rownum', $ranges)
            ->get();

        $data['total'] = $total;
        $data['per_page'] = $per_page;
        $data['blocks'] = $blocks;

        return $data;
    }

    private function applyPropertyFilter($propertyQuery, $request, $filterBuilders = true, $filterStatus = true)
    {
        if ($filterStatus) {
            if (!$request->query('showAll', false)) {
                $propertyQuery->whereNotIn('properties.status', Property::invalidStatuses);
            }
        }

        $minPrice = !empty($request->minimumPrice) ? $request->minimumPrice : 0;
        $maxPrice = !empty($request->maximumPrice) ? $request->maximumPrice : 0;

        $formats = PriceFormat::getIds();

        // when there is minimum and maximum price within the query
        if (!empty($request->minimumPrice) && !empty($request->maximumPrice)) {
            $propertyQuery->where(function ($q) use ($minPrice, $maxPrice, $formats) {
                // when the price format is price
                $q->where(function ($q) use ($minPrice, $maxPrice, $formats) {
                    $q->where('properties.price_format_id', $formats['price']);
                    $q->whereBetween('properties.price_from', [$minPrice, $maxPrice]);
                });

                $q->orWhere(function ($q) use ($minPrice, $maxPrice, $formats) {
                    $q->where('properties.price_format_id', $formats['range']);

                    $q->where(function ($q) use ($minPrice, $maxPrice) {
                        $q->whereBetween('properties.price_from', [$minPrice, $maxPrice]);
                        $q->orWhereBetween('properties.price_to', [$minPrice, $maxPrice]);
                    });
                });

                $q->orWhere('properties.price_format_id', $formats['tbd']);
            });
        }

        // when there is only minimum price within the query
        else if (!empty($request->minimumPrice) && empty($request->maximumPrice)) {
            $propertyQuery->where(function ($q) use ($minPrice, $formats) {
                $q->where('properties.price_from', '>=', $minPrice);
                $q->orWhere('properties.price_format_id', $formats['tbd']);
            });
        }

        // when there is only maximum price within the query
        else if (!empty($request->maximumPrice) && empty($request->minimumPrice)) {
            $propertyQuery->where(function ($q) use ($maxPrice, $formats) {
                $q->where(function ($q) use ($maxPrice) {
                    $q->where('properties.price_from', '<=', $maxPrice);
                    $q->whereNull('properties.price_to');
                });
                $q->orWhere('properties.price_to', '<=', $maxPrice);
                $q->orWhere('properties.price_format_id', $formats['tbd']);
            });
        }

        if ($filterStatus) {
            if ($request->listingStatus !== "any" && !empty($request->listingStatus)) {
                $propertyQuery->whereRaw('LOWER(properties.status) = ?', strtolower($request->listingStatus));
            }
        }

        if ($request->propertyTypes && count($request->propertyTypes)) {
            $propertyTypes = $request->get('propertyTypes');
            $names = [];
            foreach ($propertyTypes as $propertyType) {
                $names[] = str_replace('\\', '', $propertyType);
            }
            if (!in_array('any', $names)) {
                $categoryIds = Category::whereIn('name', $names)->pluck('id');
                $propertyQuery->whereIn('properties.category_id', $categoryIds);
            }
        }

        if ($request->bedroomsMin !== "any" && !empty($request->bedroomsMin)) {
            $propertyQuery->where('properties.bedrooms', '>=', $request->bedroomsMin);
        }

        if ($request->bathrooms !== "any" && !empty($request->bathrooms)) {
            $propertyQuery->where('properties.bathrooms_full', '>=', $request->bathrooms);
        }

        if ($request->bathroomsHalf !== "any" && !empty($request->bathroomsHalf)) {
            $propertyQuery->where('properties.bathrooms_half', '>=', $request->bathroomsHalf);
        }

        if ($request->garageOptions !== "any" && !empty($request->garageOptions)) {
            $propertyQuery->where('properties.garage_capacity', '>=', $request->garageOptions);
        }

        /**
         * builder_sqft, lot_sqft, acres
         */
        $default_lot_type = 'sqft';

        if ($request->lotType) {
            if ($request->lotType === 'builder_sqft') {
                $default_lot_type = 'properties.sqft';
            } else if ($request->lotType === 'acres') {
                $default_lot_type = 'properties.lot_size';
            }
        }

        if ($request->squareFeetMin !== 'any' && !empty($request->squareFeetMin)) {
            // $propertyQuery->where('sqft', '>=', $request->squareFeetMin);
            $propertyQuery->where($default_lot_type, '>=', $request->squareFeetMin);
        }

        if ($request->squareFeetMax !== 'any' && !empty($request->squareFeetMax)) {
            // $propertyQuery->where('sqft', '<=', $request->squareFeetMax);
            $propertyQuery->where($default_lot_type, '<=', $request->squareFeetMax);
        }

        if ($request->storiesMin !== 'any' && !empty($request->storiesMin)) {
            $propertyQuery->where('properties.stories', '>=', $request->storiesMin);
        }

        if ($request->storiesMax !== 'any' && !empty($request->storiesMax)) {
            $propertyQuery->where('properties.stories', '<=', $request->storiesMax);
        }

        if (filter_var($request->hasPool, FILTER_VALIDATE_BOOL)) {
            $propertyQuery->where('properties.has_pool', true);
        }

        if (filter_var($request->hasElevator, FILTER_VALIDATE_BOOL)) {
            $propertyQuery->where('properties.elevator_type', '!=', '0');
        }

        if ($filterBuilders && $request->selectedBuilders !== null) {
            if ($request->useInclusiveBuilders == 'true') {
                $propertyQuery->whereIn('properties.builder_id', $request->selectedBuilders);
            } else {
                $propertyQuery->whereNotIn('properties.builder_id', $request->selectedBuilders);
            }
        }

        if ($request->polygons && count($request->polygons) > 0) {
            $propertyQuery->whereIn('properties.polygon_id', $request->polygons);
        } else {
            $propertyQuery->whereNotNull('properties.polygon_id');
        }

        return $propertyQuery;
    }

    /**
     * Takes properties and sorts it as described in request
     */
    public function sortProperties(Request $request, $query, $searching = false)
    {
        if ($request->sortBy === 'price_from') {
            $query->orderBy($request->sortBy, $request->orderBy);
        } elseif ($request->sortBy === 'address') {
            $query->orderBy('properties.full_address', $request->orderBy);
        } elseif ($request->sortBy === 'neighborhood') {
            $query
                ->leftJoin('polygons', 'properties.polygon_id', '=', 'polygons.id')
                ->orderBy('polygons.title', $request->orderBy)
                ->select('properties.*');
        } elseif ($request->sortBy === 'builder') {
            $query
                ->leftJoin('builders', 'properties.builder_id', '=', 'builders.id')
                // ->orderBy('builders.name', $request->orderBy)
                // ->orderBy('properties.builder_name', $request->orderBy)
                ->select('properties.*', DB::raw('IFNULL(builders.name, properties.builder_name) AS the_builder_name'))
                ->orderBy('the_builder_name', $request->orderBy);
        } else {
            if (!$searching) {
                $query->latest();
            }
        }

        $data = ($request->query('page') === '-1') ? $query->get() : $query->paginate(12);

        foreach ($data as $property) {
            foreach ($property->media as $media) {
                $media->fullUrl = $media->getFullUrl();
            }

            foreach ($property->builder->media ?? [] as $media) {
                $media->fullUrl = $media->getFullUrl();
            }

            $property->builderPrimaryLogo = $property?->builder?->getPrimaryLogo() ?: null;
        }

        return $data;
    }

    public function getBuilderQueryInfo(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);

        $propertyQuery = PropertyResult::query()
            ->applyFilters($filters)
            ->select('builder_id')
            ->orderBy('builder_id');

        $query = Builder::query()
            ->whereIn('builders.id', $propertyQuery)
            ->select(DB::raw('id, ROW_NUMBER() OVER (ORDER BY id ASC) AS rownum'));

        $per_page = $request->query('per_page', 500);
        $total = $query->count('id');
        $ranges = $total > $per_page ? range(1, $total, $per_page) : [1];

        $blocks = Builder::query()
            ->setEagerLoads([])
            ->select('b.id', DB::raw('b.rownum'))
            ->from($query, 'b')
            ->select('b.id', 'b.rownum')
            ->whereIn('b.rownum', $ranges)
            ->get()
            ->each(fn (Builder $item) => $item->setAppends([]));

        $data['total'] = $total;
        $data['per_page'] = $per_page;
        $data['blocks'] = $blocks;

        return $this->respond($data);
    }

    public function getAssociatedBuilders(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $propertyQuery = PropertyResult::query()
            ->applyFilters($filters)
            ->select('builder_id');

        $query = Builder::query()->whereIn('builders.id', $propertyQuery);
        $builders = $query->pluck('filter_data', 'id');

        return response($builders, 200)->header('Content-Type', 'text/plain');
    }

    public function searchByAddress(Request $request)
    {
        // $search = preg_replace('/[^a-zA-Z0-9 ]/', '', strtolower($request->addressQuery));
        // $search = Property::getUnabbreviatedAddressByString($search);

        // $searchResult = Property::query()
        //     ->select('id')
        //     ->whereRaw('full_address_unabbrv like ?', ["{$search}%"])
        //     ->limit(20)
        //     ->get();

        // if ($searchResult->count() == 0) {
        //     $searchResult = Property::query()
        //         ->selectRaw('id, MATCH(full_address_unabbrv) AGAINST(? IN BOOLEAN MODE) as relevance', [$search])
        //         ->whereRaw("MATCH(full_address_unabbrv) AGAINST(? IN BOOLEAN MODE)", [$search])
        //         // ->having('relevance', '>', 0.2)
        //         // ->orderBy('relevance', 'desc')
        //         ->limit(20)
        //         ->get()
        //         ->where('relevance', '>', 10);
        // }

        $searchResult = Property::search($request->addressQuery)->get();

        $query = Property::query();

        if ($searchResult->count() == 0) {
            $query->whereNull('id');
        } else {
            $query->whereIn('id', $searchResult->pluck('id')->toArray());
            $query->where('request_status', 'approved');
        }

        $query->with(['category', 'polygon' => function ($q) {
            $q->with('ancestors:id,_lft,_rgt,zoom,slug,title');
        }, 'builder' => function ($q) {
            $q->select('builders.id', 'builders.name');
            $q->with(['media' => function ($q) {
                $q->where('collection_name', 'builder_logo');
            }]);
        }, 'media']);

        return $this->sortProperties($request, $query, true);
    }

    public function filterActiveProperties(Request $request, $builderId)
    {
        $propertyQuery = Property::query();

        if ($only_new_construction_properties = config('app.only_new_construction_properties')) {
            $propertyQuery->where('new_construction', $only_new_construction_properties);
        }

        $propertyQuery->whereNotIn('status', Property::invalidStatuses);
        $propertyQuery->where('builder_id', $builderId);

        if ($request->query('formap', false) === '1') {
            $propertyQuery = $propertyQuery->select([
                'id', 'builder_id', 'polygon_id', 'category_id', 'bedrooms',
                'bathrooms_full', 'bathrooms_half', 'garage_capacity', 'lat', 'lng',
                'title', 'status', 'sqft', 'price_from', 'price_to',
                'full_address', 'price_format_id', 'price_from', 'price_to', 'office_website',
                'estimated_completion_date', 'finance_type', 'request_status', 'type',
                'new_construction', 'path_url'
            ]);
        } else {
            $propertyQuery = $propertyQuery->with(['category', 'polygon' => function ($q) {
                $q->with('ancestors:id,_lft,_rgt,zoom,slug,title');
            }, 'builder' => function ($q) {
                $q->select('builders.id', 'builders.name');
            }, 'media']);
        }

        // Filter data as described in request
        $this->applyPropertyFilter($propertyQuery, $request);

        // Takes properties and sorts it as described in request
        return $this->sortProperties($request, $propertyQuery);
    }

    public function filterPastProperties(Request $request, $builderId)
    {
        $propertyQuery = Property::query();

        if ($only_new_construction_properties = config('app.only_new_construction_properties')) {
            $propertyQuery->where('new_construction', $only_new_construction_properties);
        }

        $propertyQuery->whereIn('status', Property::invalidStatuses);
        $propertyQuery->where('builder_id', $builderId);

        if ($request->query('formap', false) === '1') {
            $propertyQuery = $propertyQuery->select([
                'id', 'builder_id', 'polygon_id', 'category_id', 'bedrooms',
                'bathrooms_full', 'bathrooms_half', 'garage_capacity', 'lat', 'lng',
                'title', 'status', 'sqft', 'price_from',
                'full_address', 'price_format_id', 'price_to', 'office_website',
                'estimated_completion_date', 'finance_type', 'request_status', 'type',
                'new_construction', 'path_url'
            ]);
        } else {
            $propertyQuery = $propertyQuery->with(['category', 'polygon' => function ($q) {
                $q->with('ancestors:id,_lft,_rgt,zoom,slug,title');
            }, 'builder' => function ($q) {
                $q->select('builders.id', 'builders.name');
            }, 'media']);
        }

        // Filter data as described in request
        $this->applyPropertyFilter($propertyQuery, $request, true, false);

        // Takes properties and sorts it as described in request
        return $this->sortProperties($request, $propertyQuery);
    }

    public function scheduleListing(Request $request)
    {
        $today = date('Y-m-d h:i:s');

        $rules = [
            // 'scheduleDateTime' => 'required|after_or_equal:' . $today,
            'message'          => 'required',
            'name'             => 'required',
            'phone_number'     => 'required',
            'email'            => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $property = Property::query()
            ->with('builder', 'polygon.zone.ancestors')
            ->find($request->property_id);

        if ($property->status === 'Active') {
            $mailMessage = $this->composeScheduleMail($property, $request);
            $data = [
                // 'builder'        => $property->builder ? $property->builder->name : 'Unknown',
                'address'        => $property->getFullAddress(),
                'page_url'       => $property->page_url,
                'user'           => $request->name,
                'message'        => $mailMessage
            ];
            $mailTo = Setting::getBy('notification_email');
            Mail::to($mailTo)->send(new PropertySchedule($data));
            ViewingSchedule::create([
                'user_id'      => Auth::user()?->id,
                'property_id'  => $request->property_id,
                'date'         => Carbon::parse($request->scheduleDate)->toDateString(),
                'time'         => Carbon::parse($request->scheduleTime)->toTimeString(),
                'name'         => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'message'      => $request->message
            ]);

            return $this->respond('success');
        }

        return $this->respondWithError(__('property.unable_to_schedule_view'));
    }

    private function composeScheduleMail($property, $request)
    {
        return __('mail.property_schedule.lines', [
            'name'     => $request->name,
            'email'    => $request->email ?: '-',
            'phone'    => $request->phone_number ?: '-',
            'address'  => $property->getFullAddress(),
            'status'   => $property->status,
            'page_url' => $property->page_url,
            'date'     => Carbon::parse($request->scheduleDate)->toDateString(),
            'time'     => Carbon::parse($request->scheduleTime)->toTimeString(),
            'message'  => $request->message ?: '-'
        ]);
    }

    /**
     * Show the Single Property.
     *
     * @return view
     */
    public function showPreviewSingleProperty(Request $request, $property_id, $user_id, $builder_id)
    {
        $user = $request->user();
        $is_admin = $user->hasRole(['admin']);
        if ($is_admin) {
            // ...continue the process
        } else {
            $has_permission = $user->hasRole(['admin', 'builder']);
            if (!$has_permission) {
                return response()->json([
                    'success' => false,
                ]);
            }

            $managed_builder = $user->managedBuilders()->where('builder_id', $builder_id)->first();
            if (!$managed_builder) {
                return response()->json([
                    'success' => false,
                ]);
            }

            $property = $managed_builder->properties()->where('id', $property_id)->first();
            if (!$property) {
                return response()->json([
                    'success' => false,
                ]);
            }
        }

        if (!is_a_number($property_id)) {
            $property_id = rtrim($property_id, '/');
            // $url = SEOUrl::wherePath("/property/{$path}")->firstOrFail();
            $property = Property::where('path_url', "/property/{$property_id}")->firstOrFail();
            $id = $property->id;
        } else {
            $id = (int) $property_id;
        }

        $property = Property::with(['media' => function ($q) {
            $q->orderBy('order_column');
            $q->orderBy('created_at');
        }, 'builder.media', 'polygon.ancestors'])->findOrFail($id);
        $ancestors = $property->polygon->ancestors ?? [];
        $neighborhood = $property->polygon ?? null;
        $subdivision = $property->polygon ?? null;

        if (count($ancestors) > 1) {
            $subdivision = $ancestors[0] ?? $property->polygon;
        }

        $property['neighborhood'] = $neighborhood->title ?? 'N/A';
        $property['subdivision'] = $subdivision->title ?? 'N/A';
        $property['neighborhood_slug'] = $neighborhood->slug ?? null;
        $property['neighborhood_path'] = $neighborhood && $neighborhood->seourl ? $neighborhood->seourl->path : null;
        $property['subdivision_slug'] = $subdivision->slug ?? null;
        $property['subdivision_path'] = $subdivision && $subdivision->seourl ? $subdivision->seourl->path : null;
        $property['price_format_name'] = $property->priceFormat->name;
        $property['amenities'] = $property->amenities()->pluck('name')->toArray();
        $property['styles'] = $property->styles()->pluck('name')->toArray();
        $property['category'] = $property->category->name;
        $property->getFirstMedia('thumb');

        foreach ($property->media as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        foreach ($property->builder->media ?? [] as $media) {
            $media->fullUrl = $media->getFullUrl();
        }

        $property['original_request_status'] = $property->getRawOriginal('request_status');

        return $this->respond($property);
    }
}
