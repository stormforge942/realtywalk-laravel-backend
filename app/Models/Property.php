<?php

namespace App\Models;

use App\Traits\HasCompoundIndex;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Typesense\LaravelTypesense\Interfaces\TypesenseDocument;

class Property extends Model implements HasMedia, TypesenseDocument
{
    use HasMediaTrait, HasCompoundIndex, Searchable;

    public $table = 'properties';
    public static $withoutAppends = false;

    public const invalidStatuses = ['Expired', 'Withdrawn', 'Terminated', 'Sold'];
    public const pastStatuses = ['Pending', 'Sold'];
    public const validStatuses = ['Active', 'Option Pending', 'Pending', 'Pending Continue to Show', 'Under Construction', 'Completed', 'To Be Completed'];

    const TYPE_LISTING = 0;
    const TYPE_POSTING = 1;

    public $fillable = [
        'type',
        'mls_number',
        'builder_id',
        'polygon_id',
        'category_id',
        'bedrooms',
        'bathrooms_full',
        'bathrooms_half',
        'garage_capacity',
        'lat',
        'lng',
        'address_number',
        'address_street',
        'unit_number',
        'zipcode',
        'title',
        'descr',
        'agent_id',
        'agent',
        'agent_website',
        'broker_id',
        'broker',
        'office_id',
        'office_name',
        'office_website',
        'year_built',
        'sqft',
        'lot_sqft',
        'lot_size',
        'lot_front_dimension',
        'lot_back_dimension',
        'lot_left_dimension',
        'lot_right_dimension',
        'status',
        'levels',
        'price_format_id',
        'price_from',
        'price_to',
        'acres',
        'hoa_annual_fee',
        'video_embed',
        'finance_type',
        'stories',
        'school_zone_id',
        'estimated_completion_date',
        'request_status',
        'created_by',
        'new_construction',
        'builder_name',
        'is_uploading_files',
        'path_url',
        'image_urls',
        'has_pool',
        'elevator_type',
    ];

    protected $casts = [
        'id'                        => 'integer',
        'title'                     => 'string',
        'mls_number'                => 'integer',
        'builder_id'                => 'integer',
        'polygon_id'                => 'integer',
        'category_id'               => 'integer',
        'bedrooms'                  => 'integer',
        'bathrooms_full'            => 'integer',
        'bathrooms_half'            => 'integer',
        'garage_capacity'           => 'integer',
        'lat'                       => 'float',
        'lng'                       => 'float',
        'address_number'            => 'string',
        'address_street'            => 'string',
        'unit_number'               => 'string',
        'zipcode'                   => 'string',
        'descr'                     => 'string',
        'year_built'                => 'integer',
        'sqft'                      => 'integer',
        'lot_sqft'                  => 'integer',
        'lot_size'                  => 'integer',
        'lot_front_dimension'       => 'float',
        'lot_back_dimension'        => 'float',
        'lot_left_dimension'        => 'float',
        'lot_right_dimension'       => 'float',
        'status'                    => 'string',
        'levels'                    => 'integer',
        'price_format_id'           => 'integer',
        'price_from'                => 'float',
        'price_to'                  => 'float',
        'acres'                     => 'float',
        'video_embed'               => 'string',
        'finance_type'              => 'string',
        'hoa_annual_fee'            => 'boolean',
        'stories'                   => 'float',
        'estimated_completion_date' => 'string',
        'request_status'            => 'string',
        'new_construction'          => 'boolean',
        'is_uploading_files'        => 'boolean',
        'image_urls'                => 'array',
        'has_pool'                  => 'boolean',
        'map_data'                  => 'array',
    ];

    public static $rules = [
        'type'            => 'numeric|min:0|max:1',
        'lat'             => 'nullable|numeric',
        'lng'             => 'nullable|numeric',
        'title'           => 'required|min:2|max:100',
        'address_number'  => 'required|min:3|max:50',
        'address_street'  => 'required|min:3|max:250',
        'unit_number'     => 'nullable',
        'builder_id'      => 'nullable|integer',
        'category_id'     => 'nullable|integer',
        'bedrooms'        => 'nullable|integer',
        'bathrooms_full'  => 'nullable|integer',
        'bathrooms_half'  => 'nullable|integer',
        'garage_capacity' => 'nullable|integer',
        'zipcode'         => 'required|min:2|string|max:10',
        // 'mls_number'        => 'required|min:2|max:10',
        'mls_number'      => 'max:10',
        'descr'           => 'nullable|min:10|string',
        'agent'           => 'required_if:type,0|string|max:191',
        'year_built'      => 'required|date_format:Y',
        'lot_size'        => 'nullable|numeric',
        'lot_front_dimension'  => 'nullable|numeric',
        'lot_back_dimension'   => 'nullable|numeric',
        'lot_left_dimension'   => 'nullable|numeric',
        'lot_right_dimension'  => 'nullable|numeric',
        'status'          => 'nullable|string|max:40',
        'price_format_id' => 'required|integer',
        // 'price_from'        => 'nullable|regex:/[\d]{2},[\d]{2}/',
        // 'price_to'          => 'nullable|regex:/[\d]{2},[\d]{2}/',
        'price_from'                => 'nullable|min:1',
        'price_to'                  => 'nullable|min:1',
        'sqft'                      => 'nullable|regex:/^\d+(,\d+)*$/',
        'lot_sqft'                  => 'nullable|regex:/^\d+(,\d+)*$/',
        'video_embed'               => 'nullable|max:500',
        'gallery.*'                 => 'mimes:jpeg,bmp,png,webp|max:10000',
        'deletedFiles.*'            => 'nullable|string',
        'estimated_completion_date' => 'nullable|string',
        'request_status'            => 'nullable|string',
        'new_construction'          => 'boolean|nullable',
    ];

    protected $hidden = ['is_uploading_files'];

    protected $appends = [
        'page_url',
        'alt_path_url',
        'not_for_sale',
        'property_type'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return array_merge($this->toArray(), [
            'id' => (string) $this->id,
            'full_address_unabbrv' => (string) ($this->full_address_unabbrv ?: ''),
            'created_at' => $this->created_at->timestamp,
        ]);
    }

    /**
     * The Typesense schema to be created.
     *
     * @return array
     */
    public function getCollectionSchema(): array
    {
        return [
            'name' => $this->searchableAs(),
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                ],
                [
                    'name' => 'full_address_unabbrv',
                    'type' => 'string',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                ],
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

    /**
     * The fields to be queried against. See https://typesense.org/docs/0.24.0/api/search.html.
     *
     * @return array
     */
    public function typesenseQueryBy(): array
    {
        return [
            'full_address_unabbrv',
        ];
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->performOnCollections('properties');
    }

    public function seourl(): MorphOne
    {
        return $this->morphOne(SEOUrl::class, 'entity');
    }

    public function builder(): BelongsTo
    {
        return $this->belongsTo(Builder::class);
    }

    public function polygon(): BelongsTo
    {
        return $this->belongsTo(Polygon::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    /**
     * The price format that belong to the property
     */
    public function priceformat(): BelongsTo
    {
        return $this->belongsTo(PriceFormat::class, 'price_format_id');
    }

    /**
     * The styles that belong to the property
     */
    public function styles(): BelongsToMany
    {
        return $this->belongsToMany(Style::class, 'property_style');
    }

    public function favorited(): bool
    {
        $user = Auth::user();
        $user_alt = request()->user('api');
        $user_id = $user !== null
            ? $user->id : ($user_alt ? $user_alt->id : null);

        return PropertyFavorite::rowExists($user_id, $this->id);
    }

    public function getNotForSaleAttribute(): bool
    {
        if (!$this->status) {
            return true;
        }

        return in_array($this->status, self::invalidStatuses);
    }

    public function getPageUrlAttribute(): string
    {
        return $this->path_url ? url($this->path_url) : url("property/{$this->id}");
    }

    public function getAltPathUrlAttribute(): string
    {
        $path = (!empty($this->path_url) && $this->path_url[0] !== '/') ? '/' . $this->path_url : $this->path_url;

        return $path ?: "/property/{$this->id}";
    }

    public function getFullAddress(): ?string
    {
        $str = $this->full_address;

        if ($city = $this->polygon?->zone?->name) {
            $str .= ", {$city}";
        }

        if ($state = $this->polygon?->zone?->ancestors?->last()?->code) {
            $state = strtoupper($state);
            $str .= ", {$state}";
        }

        if ($zip = $this->zipcode) {
            $str .= " {$zip}";
        }

        return trim($str);
    }

    public function getPropertyTypeAttribute(): string
    {
        return $this->type ? 'Posting' : 'Listing';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeApplyFilters($query, $filters, $forceIndex = null)
    {
        if ($polygon_ids = $filters['polygon_ids']) {
            $index = $forceIndex ?: 'big_filter_w_polygons_index';
            $query->useIndex($index);
            // $query->useIndex('thebigboy2');
            $query->whereIn('properties.polygon_id', $polygon_ids);
        } else {
            $index = $forceIndex ?: 'big_filter_wo_polygons_index';
            $query->useIndex($index);
            // $query->useIndex('sfirst');
            $query->whereNotNull('properties.polygon_id');
        }

        $query->where('request_status', 'approved');

        $query->when($filters['new_construction'], function ($q) {
            $q->where('new_construction', 1);
        });

        if ($filters['show_all'] == 0) {
            $query->whereNotIn('properties.status', self::invalidStatuses);
        }

        $query->applyPricesCriteria($filters);

        if ($filters['status'] && $filters['status'] != 'any') {
            $query->whereRaw('LOWER(properties.status) = ?', strtolower($filters['status']));
        }

        if ($filters['category_ids']) {
            $query->whereIn('properties.category_id', $filters['category_ids']);
        }

        if ($filters['bedrooms_min'] && $filters['bedrooms_min'] != 'any') {
            $query->where('properties.bedrooms', '>=', $filters['bedrooms_min']);
        }

        if ($filters['bathrooms_full'] && $filters['bathrooms_full'] != 'any') {
            $query->where('properties.bathrooms_full', '>=', $filters['bathrooms_full']);
        }

        if ($filters['bathrooms_half'] && $filters['bathrooms_half'] != 'any') {
            $query->where('properties.bathrooms_half', '>=', $filters['bathrooms_half']);
        }

        if ($filters['garage_capacity'] && $filters['garage_capacity'] != 'any') {
            $query->where('properties.garage_capacity', '>=', $filters['garage_capacity']);
        }

        if ($filters['square_feet_min'] && $filters['square_feet_min'] != 'any') {
            $query->where($filters['default_lot_type'], '>=', $filters['square_feet_min']);
        }

        if ($filters['square_feet_max'] && $filters['square_feet_max'] != 'any') {
            $query->where($filters['default_lot_type'], '<=', $filters['square_feet_max']);
        }

        if ($filters['stories_min'] && $filters['stories_min'] != 'any') {
            $query->where('properties.stories', '>=', $filters['stories_min']);
        }

        if ($filters['stories_max'] && $filters['stories_max'] != 'any') {
            $query->where('properties.stories', '<=', $filters['stories_max']);
        }

        if ($filters['has_pool']) {
            $query->where('properties.has_pool', true);
        }

        if ($filters['has_elevator']) {
            $query->where('properties.elevator_type', '!=', '0');
        }

        if ($filters['has_builder_filter']) {
            if ($builder_ids = $filters['builder_ids']) {
                $query->whereIn('builder_id', $builder_ids);
            } else {
                $query->whereNull('builder_id');
            }
        }

        if ($filters['formap'] == 0) {
            $query->with(['category', 'polygon' => function ($q) {
                $q->with('ancestors:id,_lft,_rgt,zoom,slug,title');
            }, 'builder' => function ($q) {
                $q->select('builders.id', 'builders.name');
                $q->with(['media' => function ($q) {
                    $q->where('collection_name', 'builder_logo');
                }]);
            }, 'media']);
        }

        return $query;
    }

    public function scopeApplyPricesCriteria($query, $filters)
    {
        if ($filters['has_min_max_price']) {
            $formats = PriceFormat::getIds();

            $query->where(function ($q) use ($filters, $formats) {
                // when the price format is price
                $q->where(function ($q) use ($filters, $formats) {
                    $q->where('properties.price_format_id', $formats['price']);
                    $q->whereBetween('properties.price_from', [$filters['min_price'], $filters['max_price']]);
                });

                $q->orWhere(function ($q) use ($filters, $formats) {
                    $q->where('properties.price_format_id', $formats['range']);

                    $q->where(function ($q) use ($filters) {
                        $q->whereBetween('properties.price_from', [$filters['min_price'], $filters['max_price']]);
                        $q->orWhereBetween('properties.price_to', [$filters['min_price'], $filters['max_price']]);
                    });
                });

                $q->orWhere('properties.price_format_id', $formats['tbd']);
            });
        } else if ($filters['has_min_price']) {
            $formats = PriceFormat::getIds();

            $query->where(function ($q) use ($filters, $formats) {
                $q->where('properties.price_from', '>=', $filters['min_price']);
                $q->orWhere('properties.price_format_id', $formats['tbd']);
            });
        } else if ($filters['has_max_price']) {
            $formats = PriceFormat::getIds();

            $query->where(function ($q) use ($filters, $formats) {
                $q->where(function ($q) use ($filters) {
                    $q->where('properties.price_from', '<=', $filters['max_price']);
                    $q->whereNull('properties.price_to');
                });
                $q->orWhere('properties.price_to', '<=', $filters['max_price']);
                $q->orWhere('properties.price_format_id', $formats['tbd']);
            });
        }

        return $query;
    }

    public function scopeGetFromBuilder($query, $builderId)
    {
        $similarIds = Builder::whereHas('properties')->where('alias_of_builder_id', $builderId)->get()->pluck("id")->all();

        $only_new_constructions = config('app.only_new_construction_properties', null);

        $uniqueLatestPropsQuery = self::query()
            ->select(DB::raw('MAX(id) as id'))
            ->when($only_new_constructions, function ($q, $only_new_constructions) {
                return $q->where('new_construction', $only_new_constructions);
            })
            ->whereIn('builder_id', [...$similarIds, $builderId])
            ->whereNotIn('status', self::invalidStatuses)
            ->groupBy('address_number', 'address_street', 'zipcode', 'unit_number');

        $query->select('properties.*')
            ->joinSub($uniqueLatestPropsQuery, 'props', 'props.id', '=', 'properties.id')
            ->when($only_new_constructions, function ($q, $only_new_constructions) {
                return $q->where('properties.new_construction', $only_new_constructions);
            })
            ->whereIn('properties.builder_id', [...$similarIds, $builderId])
            ->whereNotIn('properties.status', self::invalidStatuses)
            ->orderBy('properties.address_number')
            ->orderBy('properties.address_street')
            ->orderBy('properties.zipcode')
            ->orderBy('properties.unit_number');

        return $query;
    }

    public function scopeGetFromPolygons($query, $polygon, $descendants)
    {
        $polygonIds = collect([$polygon->id, ...$descendants])->unique()->values()->all();
        $only_new_constructions = config('app.only_new_construction_properties', null);

        $uniqueLatestPropsQuery = self::query()
            ->select(DB::raw('MAX(id) as id'))
            ->when($only_new_constructions, function ($q, $only_new_constructions) {
                return $q->where('new_construction', $only_new_constructions);
            })
            ->whereIn('polygon_id', $polygonIds)
            ->whereNotIn('status', self::invalidStatuses)
            ->groupBy('address_number', 'address_street', 'zipcode', 'unit_number');


        $query->select('properties.*')
            ->joinSub($uniqueLatestPropsQuery, 'props', 'props.id', '=', 'properties.id')
            ->when($only_new_constructions, function ($q, $only_new_constructions) {
                return $q->where('properties.new_construction', $only_new_constructions);
            })
            ->whereIn('properties.polygon_id', $polygonIds)
            ->whereNotIn('properties.status', Property::invalidStatuses)
            ->orderBy('properties.address_number')
            ->orderBy('properties.address_street')
            ->orderBy('properties.zipcode')
            ->orderBy('properties.unit_number')
            ->get();

        return $query;
    }

    public function scopeManualPaginate($query, $page = 1, $perPage = 10)
    {
        $offset = ($page * $perPage) - $perPage;

        return $query->skip($offset)->take($perPage)->get();
    }

    public function generateSEOURL(bool $commit = true): string
    {
        $zone = Str::slug($this->polygon?->zone?->name ?: 'unknown-zone');
        $polygon = Str::slug($this->polygon?->title ?: 'no-polygon-title');
        $address = '';

        if ($full_address = $this->full_address) {
            $full_address = Str::slug($full_address);
            $address .= "$full_address";
        }

        if ($mls_number = $this->mls_number) {
            $mls_number = Str::slug($mls_number);
            $address .= "-$mls_number";
        }

        $address = $address != '' ? $address : $this->id;
        $path = "/property/{$zone}/{$polygon}/{$address}";

        if (!$commit) {
            return $path;
        }

        $validPath = $path;
        $suffix_i = 2;

        while (SEOUrl::query()
            ->where('path', $validPath)
            ->when($this->seourl, function ($query) {
                $query->where('id', '!=', $this->seourl->id);
            })
            ->exists()
        ) {
            $validPath = "$path-$suffix_i";
            $suffix_i++;
        }

        $path = $validPath;

        if ($this->seourl) {
            $this->seourl()->update(['path' => $path]);
        } else {
            $this->seourl()->create(['path' => $path]);
        }

        $this->path_url = $path;
        $this->saveQuietly();

        return $path;
    }

    public function updateAmenityFilters()
    {
        $this->updateQuietly([
            'has_pool' => DB::raw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM property_amenity pa
                        JOIN amenities a ON a.id = pa.amenity_id
                        WHERE pa.property_id = {$this->id}
                        AND a.name IN ('Private Pool', 'Area Pool')
                    ) THEN true
                    ELSE false
                END
            "),
            'elevator_type' => DB::raw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM property_amenity pa
                        JOIN amenities a ON a.id = pa.amenity_id
                        WHERE pa.property_id = {$this->id}
                        AND a.name = 'Elevator Shaft'
                    ) THEN '2'
                    WHEN EXISTS (
                        SELECT 1
                        FROM property_amenity pa
                        JOIN amenities a ON a.id = pa.amenity_id
                        WHERE pa.property_id = {$this->id}
                        AND a.name = 'Elevator'
                    ) THEN '1'
                    ELSE false
                END
            "),
        ]);
    }

    /**
     * Initialize the suffix map from JSON file
     *
     * @return array
     */
    private static function getSuffixMap(): array
    {
        try {
            $jsonContent = Storage::get('street-suffixes.json');
            $suffixData = json_decode($jsonContent, true);

            // Convert the JSON data into a simple key-value map
            $results = [];
            foreach ($suffixData as $entry) {
                // Add the short form as key
                $results[$entry['sh']] = $entry['ln'];
                $results[$entry['sh'] . '.'] = $entry['ln'];

                // Add all variations from the 'ls' array as keys
                foreach ($entry['ls'] as $variant) {
                    $results[$variant] = $entry['ln'];
                    $results[$variant . '.'] = $entry['ln'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load street suffix data: ' . $e->getMessage());
            $results = [];
        }

        return $results;
    }

    /**
     * Convert abbreviated street address to full form
     *
     * @param string|null $address
     * @return string
     */
    public function getUnabbreviatedAddress(): string
    {
        $new_street_address = static::getUnabbreviatedAddressByString($this->address_street);
        $full_address = '';

        if ($address_number = trim($this->address_number)) {
            $full_address .= $address_number;
        }

        $dir_prefix = $this->address_street_dir_prefix ?: '';
        $dir_suffix = $this->address_street_dir_suffix ?: '';
        $prefix_directional = str_contains($this->address_street, 'North ')
            || str_contains($this->address_street, 'West ')
            || str_contains($this->address_street, 'East ')
            || str_contains($this->address_street, 'South ');
        $suffix_directional = str_contains($this->address_street, ' North')
            || str_contains($this->address_street, ' West')
            || str_contains($this->address_street, ' East')
            || str_contains($this->address_street, ' South');

        if (!(trim($dir_prefix) == '' || $prefix_directional)) {
            $full_address .= ' '.trim($dir_prefix);
        }

        $full_address .= ' ' . $new_street_address;

        if (!(trim($dir_suffix) == '' || $suffix_directional)) {
            $full_address .= ' ' . trim($dir_suffix);
        }

        if ($address_street_suffix = trim($this->address_street_suffix)) {
            $full_address .= ' '.$address_street_suffix;
        }

        if ($unit_number = trim($this->unit_number)) {
            $full_address .= ' ' . $unit_number;
        }

        return strtolower($full_address);
    }

    public static function getUnabbreviatedAddressByString(string $street_address): string
    {
        if (empty($street_address)) {
            return '';
        }

        $suffixMap = static::getSuffixMap();
        $saintNames = [
            'AGNES', 'ALBANS', 'ANDREWS', 'ANNE', 'ANTHONY', 'AUGUSTINE',
            'BARBARA', 'BERNARD', 'BONIFACE', 'CATHERINE', 'CECILIA',
            'CHARLES', 'CHRISTOPHER', 'CLAIRE', 'CLEMENT', 'CLOUD',
            'DAVID', 'DENIS', 'DOMINIC', 'EDWARD', 'ELIZABETH', 'FRANCIS',
            'GEORGE', 'GERARD', 'GREGORY', 'HELENA', 'HENRY', 'IGNATIUS',
            'JAMES', 'JEROME', 'JOAN', 'JOHN', 'JOSEPH', 'LAWRENCE',
            'LEONARD', 'LOUIS', 'LOUISE', 'LUCIA', 'LUKE', 'MARGARET',
            'MARIA', 'MARK', 'MARTIN', 'MARY', 'MATTHEW', 'MICHAEL',
            'NICHOLAS', 'PATRICK', 'PAUL', 'PETER', 'PHILIP', 'PIERRE',
            'RAYMOND', 'RITA', 'ROSE', 'STEPHEN', 'TERESA', 'THERESE',
            'THOMAS', 'VINCENT'
        ];

        // Split the address into words
        $words = preg_split('/\s+/', trim($street_address));

        // Process each word
        for ($i = 0; $i < count($words); $i++) {
            $word = strtoupper($words[$i]);
            $wordWithoutDot = rtrim($word, '.');

            // Handle St prefix cases
            if ($wordWithoutDot === 'ST') {
                // Case 1: ST prefix before a Saint name at the start
                if ($i === 0 && count($words) > 1 && in_array(strtoupper($words[1]), $saintNames)) {
                    continue;
                }

                // Case 2: ST prefix before Highway/HWY
                if (isset($words[$i + 1])) {
                    $nextWord = strtoupper($words[$i + 1]);
                    $highwayVariants = ['HWY', 'HWY.', 'HIGHWAY', 'HIWAY', 'HIGHWY', 'HWAY'];
                    $isHighway = in_array($nextWord, $highwayVariants);
                    if (count($words) > $i + 1 && $isHighway) {
                        $words[$i] = 'State';
                        continue;
                    }
                }
            }

            // Regular suffix replacement
            if (isset($suffixMap[$word])) {
                // Preserve the original case if the word was not all uppercase
                if ($words[$i] !== strtoupper($words[$i])) {
                    $words[$i] = ucfirst(strtolower($suffixMap[$word]));
                } else {
                    $words[$i] = $suffixMap[$word];
                }
            }
        }

        // Rejoin the words
        return strtolower(implode(' ', $words));
    }

    protected function getArrayableAppends()
    {
        if(self::$withoutAppends) return [];

        return parent::getArrayableAppends();
    }
}
