<?php

namespace App\Repositories;

use App\Models\Property;
use App\Repositories\BaseRepository;

/**
 * Class PropertyRepository
 * @package App\Repositories
 * @version February 28, 2020, 1:42 pm UTC
*/

class PropertyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'lat',
        'lng',
        'address_number',
        'address_street',
        'unit_number',
        'zipcode',
        'title',
        'descr',
        'style',
        'year_built',
        'lot_size',
        'bedrooms',
        'bathrooms_full',
        'bathrooms_half',
        'status',
        'price_from',
        'price_to',
        'youtube_embed'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Property::class;
    }
}
