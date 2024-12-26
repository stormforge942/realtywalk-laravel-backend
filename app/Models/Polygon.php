<?php

namespace App\Models;

use App\Traits\HasCompoundIndex;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Polygon extends Model implements HasMedia
{
    use NodeTrait, HasMediaTrait, HasCompoundIndex, Searchable {
        NodeTrait::usesSoftDelete insteadof Searchable;
    }

    const CACHE_TREE_FILE = 'neighborhood-tree.cache.json';
    const CACHE_ZOOM1_FILE = 'polygons-level1.cache.json';

    public $table = 'polygons';

    public $fillable = [
        'zone_id',
        'slug',
        'color',
        'title',
        'zoom',
        'area',
        'min_lat',
        'min_lng',
        'max_lat',
        'max_lng',
        'geometry',
        'parent_id',
        '_lft',
        '_rgt',
        'code',
        'description',
        'links',
        'extId',
        'display_as_background',
        'updated_at',
        'is_uploading_files'
    ];

    protected $casts = [
        'id'    => 'integer',
        'color' => 'string',
        'title' => 'string',
        'zoom'  => 'integer',
        'is_uploading_files' => 'boolean'
    ];

    protected $hidden = ['geometry', 'geometry_json', 'is_uploading_files'];

    public static $rules = [
        'zone_id'            => 'required',
        'title'              => 'required|max:100',
        'geoJson'            => 'required|json',
        'code'               => 'unique:polygons,code',
        'zoom'               => 'required|numeric|min:0|max:22',
        'statistics.*.value' => 'nullable|numeric',
        'featured_image.*'   => 'mimes:jpeg,bmp,png,webp|max:10000',
        'gallery.*'          => 'mimes:jpeg,bmp,png,webp|max:10000',
    ];

    protected $with = ['seourl'];

    protected $appends = [
        'page_url',
        'path_url',
        'link_list',
        'featured_image_url',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->performOnCollections('polygons', 'featured_image');
    }

    public function getPageUrlAttribute(): string
    {
        return $this->relationLoaded('seourl') && $this->seourl
            ? url($this->seourl->path) : url('neighborhood/' . $this->id);
    }

    public function getPathUrlAttribute(): string
    {
        if (!$this->relationLoaded('seourl')) {
            return "/neighborhood/{$this->id}";
        }

        if (!$this->seourl) {
            return "/neighborhood/{$this->id}";
        }

        $path = $this->seourl->path;

        return (!empty($path) && $path[0] !== '/') ? '/' . $path : $path;
    }

    public function seourl(): MorphOne
    {
        return $this->morphOne(SEOUrl::class, 'entity');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function builders(): BelongsToMany
    {
        return $this->belongsToMany(Builder::class);
    }

    public function statistics(): BelongsToMany
    {
        return $this->belongsToMany(Statistic::class, 'polygon_statistic')->withPivot('value');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function setCreatedAt($value): void
    {
        // Override default, do nothing. created_at is disabled, but updated_at is used.
    }

    public function getCreatedAt()
    {
        // Override default, return null. created_at is disabled, but updated_at is used.
        return null;
    }

    public function getChildLinksAttribute()
    {
        $children = $this->where('parent_id', $this->id)->get();

        return $children->flatMap(fn($child) => $child->link_list);
    }

    public function getChildLinksRecursive($polygon)
    {
        // $siblings = $this->where('parent_id', $polygon->parent_id)->get();
        // $siblingsWithLinks = $siblings->filter(fn($sibling) => $sibling->link_list && count($sibling->link_list) > 0);
    
        // if ($siblingsWithLinks->isNotEmpty() && $polygon->zoom === 3) return [];
        // if ($polygon->link_list && count($polygon->link_list) > 0) return $polygon->link_list;

        // $children = $this->where('parent_id', $polygon->id)->get();
        // if ($children->isEmpty()) return [];

        // $links = $children->flatMap(fn($child) => $this->getChildLinksRecursive($child));
        // return $links;
    }

    public function getLinkListAttribute()
    {
        if (is_null($this->links)) {
            return [];
        }

        $data = [];
        $links = is_array($this->links) ? $this->links : unserialize($this->links);
        $links = collect($links)->reject(function ($link) {
            return is_null($link);
        })->values()->all();

        foreach ($links as $link) {
            $data[] = [
                'label'       => $link['label'] ?? '',
                'title'       => $link['title'] ?? '',
                'url'         => $link['url'] ?? $link,
                'image'       => $link['image'] ?? '',
                'description' => $link['description'] ?? '',
                'status'      => $link['status'] ?? '',
            ];
        }

        return $data;
    }

    public function getFeaturedImageUrlAttribute()
    {
        // if ($this->relationLoaded('media')) {
        //     return null;
        // }

        $featured_image = $this->media?->where('collection_name', 'featured_image')->first();

        return $featured_image ? $featured_image->getFullUrl() : null;
    }

    public function getAutomaticColor(): string
    {
        if ($this->zoom == 2 && $this->parent_id && $parentColor = $this->parent()->pluck('color')[0] ?? null) {
            return $parentColor;
        }

        $code = $this->code ?: $this->title;

        return '#' . substr(md5($code ?? uniqid()), 0, 6);
    }

    public function canEditColor(): bool
    {
        return $this->zoom != 2 || $this->parent_id == null;
    }

    public function getChildren($polygon)
    {
        $zoom = $polygon->zoom;
        $polygons = $this->getChildrenRecursively($polygon->id, $zoom);

        return $polygons;
    }

    public function getChildrenRecursively($id, $zoom = 1)
    {
        $result = [$id];
        $polygons = $this->whereIn('parent_id', $this->getAllParentIds($id, $zoom))->get();
        $polygonMap = $polygons->groupBy('parent_id');
        $this->addChildren($id, $zoom, $result, $polygonMap);

        return $result;
    }

    private function getAllParentIds($id, $zoom)
    {
        $parentIds = [$id];
        $polygons = $this->where('parent_id', $id)->get();

        while ($zoom > 1 && $polygons->isNotEmpty()) {
            $ids = $polygons->pluck('id');
            $parentIds = array_merge($parentIds, $ids->toArray());

            $polygons = $this->whereIn('parent_id', $ids)->get();
            $zoom--;
        }

        return $parentIds;
    }

    private function addChildren($parentId, $zoom, &$result, $polygonMap)
    {
        if ($zoom <= 0 || !isset($polygonMap[$parentId])) {
            return;
        }

        foreach ($polygonMap[$parentId] as $polygon) {
            $result[] = $polygon->id;
            $this->addChildren($polygon->id, $zoom - 1, $result, $polygonMap);
        }
    }

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

    public function scopeIntersectsPolygon($query, $polygon)
    {
        // Original formula if (RectA.X1 < RectB.X2 && RectA.X2 > RectB.X1 &&
        // RectA.Y1 > RectB.Y2 && RectA.Y2 < RectB.Y1)
        return $query->intersectsBounds([
            $polygon->min_lat,
            $polygon->min_lng,
            $polygon->max_lat,
            $polygon->max_lng
        ]);
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

        DB::statement('UPDATE polygons set area = ST_Area(geometry)*40075, min_lat = ?, min_lng = ?, max_lat = ?, max_lng = ? where id = ?', [
            $minLat,
            $minLng,
            $maxLat,
            $maxLng,
            $this->id,
        ]);

        $this->refresh();
    }

    public static function getDBRawSize()
    {
        return DB::raw('(max_lat - min_lat) * (max_lng - min_lng)');
    }

    public function updateSlug()
    {
        $newSlug = Str::slug($this->title);

        if (Polygon::where('slug', $newSlug)->where('id', '!=', $this->id)->exists()) {
            $newSlug .= '-' . $this->id;
        }

        $this->update(['slug' => $newSlug]);
    }

    public function assignParent()
    {
        if ($this->zoom == 1) {
            $this->makeTopNode();
            return;
        }

        $parent = Polygon::where('id', '!=', $this->id)
            ->whereNotNull('geometry')
            ->where('zoom', '<', $this->zoom)
            ->where('area', '>=', $this->area)
            ->whereRaw('ST_Intersects(geometry, (SELECT geometry from polygons where id = ' . $this->id . ' limit 1))')
            ->orderBy('area')
            ->first();

        if ($parent) {
            $this->update(['parent_id' => $parent->id]);
            Log::debug('Assigned ' . $this->id . ' to parent ' . $parent->id);
        } else {
            $this->saveAsRoot();
        }
    }

    private function makeTopNode($zoom = 1)
    {
        try {
            $this->update(['zoom' => $zoom]);
            $this->saveAsRoot();
        } catch (\Exception $e) {
            try {
                Polygon::fixTree();
                $this->saveAsRoot();
            } catch (\Exception $e) {
                Log::error('Failed to save polygon as root: ' . $this->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function generateSEOURL(bool $commit = true): string
    {
        $items = $this->ancestors?->pluck('slug')?->all() ?: [];
        $items[] = $this->slug;

        $slug = $items ? implode('/', $items) : $this->id;
        $path = "/neighborhood/$slug";

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

    public static function fetchLinksDetails(?array $input_links)
    {
        if (!$input_links) {
            return null;
        }

        $labels = $input_links['label'];
        $links = [];

        foreach ($labels as $index => $label) {
            $url = $input_links['url'][$index];

            if (!$label && !$url) {
                continue;
            }

            $links[] = self::fetchLink([
                'label' => $label,
                'url' => $url,
            ]);
        }

        return serialize($links);
    }

    public static function fetchLink($link)
    {
        $url = isset($link['url']) ? $link['url'] : (is_string($link) ? $link : null);
        $details = [
            'url' => $url,
            'label' => !empty($link['label']) ? $link['label'] : '',
        ];

        if (!$url) return $details;

        try {
            $metaTags = [];
            $userAgents = [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.5845.96 Safari/537.36',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:116.0) Gecko/20100101 Firefox/116.0',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:116.0) Gecko/20100101 Firefox/116.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:116.0) Gecko/20100101 Firefox/116.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:116.0) Gecko/20100101 Safari/537.36',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 15_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6 Mobile/15E148 Safari/604.1',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.5845.96 Safari/537.36 Edg/116.0.0.0',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.5845.96 Safari/537.36 OPR/100.0.0.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:100.0) Gecko/20100101 Firefox/100.0',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; Trident/7.0; AS; rv:11.0) like Gecko',
            ];
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => array_rand($userAgents)])
                ->withOptions(['allow_redirects' => ['max' => 10]])
                ->get($url);
            $html = (string) $response->getBody();
            $statusCode = $response->getStatusCode();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);

            $metaProperties = [
                'og:title' => 'property',
                'og:description' => 'property',
                'og:image' => 'property',
                'twitter:title' => 'name',
                'twitter:description' => 'name',
                'twitter:image' => 'name',
                'description' => 'name',
            ];

            foreach ($metaProperties as $key => $type) {
                $metaTags[$key] = null;

                $metaElements = $dom->getElementsByTagName('meta');

                foreach ($metaElements as $meta) {
                    if ($meta->hasAttribute($type) && $meta->getAttribute($type) === $key) {
                        $metaTags[$key] = $meta->getAttribute('content');
                    }
                }
            }

            $titleTag = $dom->getElementsByTagName('title');
            $details['title'] = $titleTag->length > 0 ? $titleTag->item(0)->textContent : ($metaTags['og:title'] ?? $metaTags['twitter:title']);
            $details['description'] = $metaTags['description'] ?? $metaTags['og:description'] ?? $metaTags['twitter:description'];
            $details['image'] = $metaTags['og:image'] ?? $metaTags['twitter:image'];
            $details['status'] = $statusCode;

            return $details;
        } catch (\Throwable $th) {
            // throw $th;
            $details["error"] = $th->getMessage();
        }

        return $details;
    }
}
