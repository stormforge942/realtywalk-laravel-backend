<?php

namespace App\Repositories;

use App\Models\Amenity;
use App\Repositories\BaseRepository;

/**
 * Class AmenityRepository
 * @package App\Repositories
 * @version March 10, 2020, 5:28 am UTC
*/

class AmenityRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
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
        return Amenity::class;
    }
}
