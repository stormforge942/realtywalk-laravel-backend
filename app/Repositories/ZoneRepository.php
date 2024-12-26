<?php

namespace App\Repositories;

use App\Models\Zone;
use App\Repositories\BaseRepository;

/**
 * Class ZoneRepository
 * @package App\Repositories
 * @version March 6, 2020, 8:01 am UTC
*/

class ZoneRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'lat',
        'lng'
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
        return Zone::class;
    }
}
