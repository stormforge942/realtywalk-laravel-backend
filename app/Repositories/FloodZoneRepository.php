<?php

namespace App\Repositories;

use App\Models\FloodZone;
use App\Repositories\BaseRepository;

/**
 * Class FloodZoneRepository
 * @package App\Repositories
 * @version March 1, 2023, 8:01 am UTC
 */

class FloodZoneRepository extends BaseRepository
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
        return FloodZone::class;
    }
}
