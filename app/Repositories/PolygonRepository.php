<?php

namespace App\Repositories;

use App\Models\Polygon;
use App\Repositories\BaseRepository;

/**
 * Class PolygonRepository
 * @package App\Repositories
 * @version March 5, 2020, 12:40 pm UTC
*/

class PolygonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'color',
        'title',
        'lat',
        'lng',
        'zoom'
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
        return Polygon::class;
    }
}
