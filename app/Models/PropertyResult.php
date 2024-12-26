<?php

namespace App\Models;

use App\Traits\HasCompoundIndex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyResult extends Model
{
    use HasFactory, HasCompoundIndex;

    public $timestamps = false;

    public const invalidStatuses = ['Expired', 'Withdrawn', 'Terminated', 'Sold'];
    public const pastStatuses = ['Pending', 'Sold'];
    public const validStatuses = ['Active', 'Option Pending', 'Pending', 'Pending Continue to Show', 'Under Construction', 'Completed', 'To Be Completed'];

    const TYPE_LISTING = 0;
    const TYPE_POSTING = 1;

    protected $fillable = [
        'full_address',
        'type',
        'status',
        'path_url',
        'lat',
        'lng',
        'descr',
        'bedrooms',
        'bathrooms_full',
        'bathrooms_half',
        'garage_capacity',
        'sqft',
        'lot_size',
        'stories',
        'price_format_id',
        'price_from',
        'price_to',
        'has_pool',
        'elevator_type',
        'category_id',
        'category_name',
        'property_id',
        'polygon_id',
        'polygon_name',
        'polygon_path',
        'builder_id',
        'builder_name',
        'builder_path',
        'primary_image',
        'new_construction',
        'estimated_completion_date',
        'map_data',
        'created_at',
    ];

    protected $casts = [
        'title'                     => 'string',
        'mls_number'                => 'integer',
        'posting_number'            => 'string',
        'builder_id'                => 'integer',
        'polygon_id'                => 'integer',
        'category_id'               => 'integer',
        'bedrooms'                  => 'integer',
        'bathrooms_full'            => 'integer',
        'bathrooms_half'            => 'integer',
        'garage_capacity'           => 'integer',
        'lat'                       => 'float',
        'lng'                       => 'float',
        'descr'                     => 'string',
        'sqft'                      => 'integer',
        'lot_size'                  => 'integer',
        'status'                    => 'string',
        'price_format_id'           => 'integer',
        'price_from'                => 'float',
        'price_to'                  => 'float',
        'stories'                   => 'float',
        'estimated_completion_date' => 'string',
        'new_construction'          => 'boolean',
        'image_urls'                => 'array',
        'map_data'                  => 'array',
        'list_data'                 => 'array',
    ];

    public function scopeApplyFilters($query, $filters, $forceIndex = null)
    {
        $polygon_ids = $filters['polygon_ids'];

        if ($polygon_ids && !$filters['has_filters']) {
            $index = $forceIndex ?: 'filter_w_polygons_pr_index';
            $query->useIndex($index);
            $query->whereIn('polygon_id', $polygon_ids);
        } else {
            $index = $forceIndex ?: 'filter_wo_polygons_pr_index';
            $query->useIndex($index);
            $query->whereNotNull('polygon_id');
        }

        if ($polygon_ids = $filters['polygon_ids']) {
            $query->whereIn('polygon_id', $polygon_ids);
        } else {
            $query->whereNotNull('polygon_id');
        }

        $query->when($filters['new_construction'], function ($q) {
            $q->where('new_construction', 1);
        });

        $query->applyPricesCriteria($filters);

        if ($filters['status'] && $filters['status'] != 'any') {
            $query->whereRaw('status = ?', strtolower($filters['status']));
        } else {
            $query->whereRaw('(status IS NOT NULL or status IS NULL)');
        }

        if ($filters['category_ids']) {
            $query->whereIn('category_id', $filters['category_ids']);
        }

        if ($filters['bedrooms_min'] && $filters['bedrooms_min'] != 'any') {
            $query->where('bedrooms', '>=', $filters['bedrooms_min']);
        }

        if ($filters['bathrooms_full'] && $filters['bathrooms_full'] != 'any') {
            $query->where('bathrooms_full', '>=', $filters['bathrooms_full']);
        }

        if ($filters['bathrooms_half'] && $filters['bathrooms_half'] != 'any') {
            $query->where('bathrooms_half', '>=', $filters['bathrooms_half']);
        }

        if ($filters['garage_capacity'] && $filters['garage_capacity'] != 'any') {
            $query->where('garage_capacity', '>=', $filters['garage_capacity']);
        }

        $default_lot_type = str_replace("properties", "property_results", $filters['default_lot_type']);
        if ($filters['square_feet_min'] && $filters['square_feet_min'] != 'any') {
            $query->where($default_lot_type, '>=', $filters['square_feet_min']);
        }

        if ($filters['square_feet_max'] && $filters['square_feet_max'] != 'any') {
            $query->where($default_lot_type, '<=', $filters['square_feet_max']);
        }

        if ($filters['stories_min'] && $filters['stories_min'] != 'any') {
            $query->where('stories', '>=', $filters['stories_min']);
        }

        if ($filters['stories_max'] && $filters['stories_max'] != 'any') {
            $query->where('stories', '<=', $filters['stories_max']);
        }

        if ($filters['has_pool']) {
            $query->where('has_pool', true);
        }

        if ($filters['has_elevator']) {
            $query->where('elevator_type', '!=', '0');
        }

        if ($filters['has_builder_filter']) {
            if ($builder_ids = $filters['builder_ids']) {
                $query->whereIn('builder_id', $builder_ids);
            } else {
                $query->whereNull('builder_id');
            }
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
                    $q->where('price_format_id', $formats['price']);
                    $q->whereBetween('price_from', [$filters['min_price'], $filters['max_price']]);
                });

                $q->orWhere(function ($q) use ($filters, $formats) {
                    $q->where('price_format_id', $formats['range']);

                    $q->where(function ($q) use ($filters) {
                        $q->whereBetween('price_from', [$filters['min_price'], $filters['max_price']]);
                        $q->orWhereBetween('price_to', [$filters['min_price'], $filters['max_price']]);
                    });
                });

                $q->orWhere('price_format_id', $formats['tbd']);
            });
        } else if ($filters['has_min_price']) {
            $formats = PriceFormat::getIds();

            $query->where(function ($q) use ($filters, $formats) {
                $q->where('price_from', '>=', $filters['min_price']);
                $q->orWhere('price_format_id', $formats['tbd']);
            });
        } else if ($filters['has_max_price']) {
            $formats = PriceFormat::getIds();

            $query->where(function ($q) use ($filters, $formats) {
                $q->where(function ($q) use ($filters) {
                    $q->where('price_from', '<=', $filters['max_price']);
                    $q->whereNull('price_to');
                });
                $q->orWhere('price_to', '<=', $filters['max_price']);
                $q->orWhere('price_format_id', $formats['tbd']);
            });
        }

        return $query;
    }

    public function scopeSortProperties($query, Request $request)
    {
        if ($request->sortBy === 'price_from') {
            $query->orderBy($request->sortBy, $request->orderBy);
        } elseif ($request->sortBy === 'address') {
            $query->orderBy('full_address', $request->orderBy);
        } elseif ($request->sortBy === 'neighborhood') {
            $query->orderBy('polygon_name', $request->orderBy);
        } elseif ($request->sortBy === 'builder') {
            $query->orderBy('builder_name', $request->orderBy);
        } else {
            $query->latest();
        }

        if ($request->query('page') === '-1') {
            return $query->pluck('list_data');
        }

        $count = $query->count();
        $currentPage = $request->query('page', 1);
        $perPage = 12;
        $currentItems = $query->take($perPage)->offset(($currentPage - 1) * $perPage)->pluck('list_data');
        $paginator = new LengthAwarePaginator($currentItems, $count, $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return $paginator;
    }
}
