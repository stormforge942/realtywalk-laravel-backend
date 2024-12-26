<?php

namespace App\Repositories;

use App\Models\StatisticType;
use App\Repositories\BaseRepository;

/**
 * Class StatisticTypeRepository
 * @package App\Repositories
 * @version March 14, 2020, 8:25 am UTC
*/

class StatisticTypeRepository extends BaseRepository
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
        return StatisticType::class;
    }
}
