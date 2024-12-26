<?php

namespace App\Repositories;

use App\Models\SchoolZone;
use App\Repositories\BaseRepository;

/**
 * Class SchoolZoneRepository
 * @package App\Repositories
 * @version March 1, 2023, 8:01 am UTC
*/

class SchoolZoneRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'type',
        'zoom',
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
        return SchoolZone::class;
    }
}
