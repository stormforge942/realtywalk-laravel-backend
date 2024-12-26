<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Builder extends Model implements HasMedia
{
    use Searchable, HasMediaTrait;

    public $table = 'builders';

    public $fillable = [
        'name',
        'slug',
        'descr',
        'profile_headline',
        'email',
        'address',
        'address1',
        'address2',
        'address3',
        'city',
        'phone',
        'website',
        'hidden',
        'alias_of_builder_id',
        'is_uploading_files'
    ];

    protected $casts = [
        'id'       => 'integer',
        'name'     => 'string',
        'slug'     => 'string',
        'descr'    => 'string',
        'email'    => 'string',
        'address'  => 'string',
        'address1' => 'string',
        'address2' => 'string',
        'address3' => 'string',
        'city'     => 'string',
        'phone'    => 'string',
        'website'  => 'string',
        'hidden'   => 'boolean',
        'is_uploading_files' => 'boolean',
        'filter_data' => 'array',
    ];

    public static $rules = [
        'name'        => 'required|max:50',
        'slug'        => 'unique:builders,slug|max:50',
        'descr'       => 'nullable|max:1000',
        'email'       => 'nullable|email|max:128',
        'address'     => 'nullable|max:350',
        'address1'    => 'nullable|max:128',
        'address2'    => 'nullable|max:128',
        'address3'    => 'nullable|max:128',
        'city'        => 'nullable|max:128',
        'phone'       => 'nullable|max:12',
        'website'     => 'nullable|url|max:128',
        'logo.*'      => 'mimes:jpeg,bmp,png,webp|max:10000',
        'gallery.*'   => 'mimes:jpeg,bmp,png,webp|max:10000',
    ];

    protected $hidden = ['is_uploading_files'];

    protected $appends = ['page_url', 'path_url'];

    protected $with = ['seourl'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->performOnCollections('builders');
    }

    public function seourl(): MorphOne
    {
        return $this->morphOne(SEOUrl::class, 'entity');
    }

    public function polygons(): BelongsToMany
    {
        return $this->belongsToMany(Polygon::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function aliasOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'alias_of_builder_id');
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(self::class, 'alias_of_builder_id');
    }

    public function getPageUrlAttribute(): string
    {
        return $this->relationLoaded('seourl') && $this->seourl
            ? url($this->seourl->path) : url('builder/' . $this->id);
    }

    public function getPathUrlAttribute(): string
    {
        if (!$this->relationLoaded('seourl')) {
            return "/builder/{$this->id}";
        }

        if (!$this->seourl) {
            return "/builder/{$this->id}";
        }

        $path = $this->seourl->path;

        return (!empty($path) && $path[0] !== '/') ? '/' . $path : $path;
    }

    public function getPrimaryLogo(): ?string
    {
        if ($this->relationLoaded('media')) {
            $logo = $this->media->where('collection_name', 'builder_logo')->sortBy('order_column')->first();

            return $logo ? $logo->getFullUrl() : null;
        }

        return null;
    }

    public static function generateSlug($title, $max_char = 50, $except = null): string
    {
        $slug = Str::slug($title);
        $allSlugs = static::query();
        $allSlugs->select('slug')->where('slug', 'like', $slug . '%');

        if ($except) {
            $allSlugs->where('id', '!=', $except);
        }

        $allSlugs = $allSlugs->get();

        if (!$allSlugs->contains('slug', $slug)) {
            return $slug;
        }

        for ($i = 1; $i <= 50; $i++) {
            $suffix = '-' . $i;
            $slug = substr($slug, 0, $max_char - strlen($suffix));
            $newSlug = $slug . $suffix;

            if (!$allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }
    }


    /*
        - Fetch first 15 rows
        - Loop through them
        - Check if some id exists in $skipIds array (With this one, is known if some row name was similar to one and it is not needed to be checked
            again)
        - Verify each row if it have similar names with next ones
            - For example row[0] will be checked with row[1], row[2] ..... and so on
            - If they are found, they are inserted into $similars and $skipIds array
            - If similar array is > 0, row id in question is inserted into parentId arrat with its corresponding similar ids,
            - If not row ids are inserted into $noSkipped array, this one is used to known which ones dont' have similar names
        - If $parentIds > 0:
            - Loop through them to combine each parent with its corresponding similars
    */
    public function scopeGetBuildersV2($query, $request, $page = 1, $perPage = 15, $keyword = "", $searchByText = false)
    {
        $builders = $query->select(['id', 'name', 'slug'])->whereHas('properties')->where(function ($query) use ($keyword, $searchByText) {
            if (!$searchByText) {
                // search with keywords
                if (!empty($keyword)) {
                    if ($keyword == 'numeric') {
                        $query->whereRaw("name regexp '^[0-9]+'");
                    } else {
                        $query->where('name', 'like', "$keyword%");
                    }
                }

                return $query->where("name", "!=", "");
            } else {
                // search with text
                while (strpos($keyword, ' ') !== false) {
                    $keyword = str_replace(' ', '%', $keyword);
                }

                return $query->where('name', 'like', "%$keyword%");
            }
        })->orderBy('name')->get();

        $builders = $builders->map(function ($builder) {
            $builder = (object) $builder;
            $builder->properties = Property::where('builder_id', $builder->id)->take(15)->get();
            $builder->properties_pagination = [
                'total' => count($builder->properties),
                'page'  => 1,
            ];

            return $builder;
        })->toArray();

        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(
            array_slice($builders, $offset, $perPage, false),
            count($builders),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );
    }

    public function scopeGetBuilders($query, $request, $page = 1, $perPage = 15, $keyword = "", $searchByText = false)
    {
        $offset = ($page * $perPage) - $perPage;

        $builders = $query->select(['id', 'name', 'slug'])->whereHas('properties')->where(function ($query) use ($keyword, $searchByText) {
            if (!$searchByText) {
                // search with keywords
                if (!empty($keyword)) {
                    if ($keyword == 'numeric') {
                        $query->whereRaw("name regexp '^[0-9]+'");
                    } else {
                        $query->where('name', 'like', "$keyword%");
                    }
                }

                return $query->where("name", "!=", "");
            } else {
                // search with text
                while (strpos($keyword, ' ') !== false) {
                    $keyword = str_replace(' ', '%', $keyword);
                }

                return $query->where('name', 'like', "%$keyword%");
            }
        })->whereNull('alias_of_builder_id')->orderBy('name')->skip($offset)->take($perPage)->paginate();

        return $builders->through(function ($builder) {
            $propQuery = Property::getFromBuilder($builder->id);
            $propCount = $propQuery->count();
            $builder->properties = $propQuery->manualPaginate();
            $builder->properties_pagination = self::propertiesPagination($propCount);

            return $builder;
        });
    }

    public static function propertiesPagination($total = 0, $page = 1, $perPage = 10)
    {
        return [
            'total' => (int)$total,
            'page' => (int)$page,
            'perPage' => (int)$perPage,
        ];
    }

    public function generateSEOURL($commit = true): string
    {
        if ($slug = $this->slug) {
            $path = Str::slug($slug);
        } else {
            $path = ($name = $this->name)
                ? Str::slug($name)
                : "builder-{$this->id}";
        }

        $path = "/builder/{$path}";

        if (!$commit) {
            return $path;
        }

        $suffix_i = 2;
        $validPath = $path;

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

        return $path;
    }
}
