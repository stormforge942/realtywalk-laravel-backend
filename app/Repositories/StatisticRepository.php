<?php

namespace App\Repositories;

use App\Models\Statistic;
use App\Repositories\BaseRepository;

/**
 * Class StatisticRepository
 * @package App\Repositories
 * @version March 14, 2020, 8:27 am UTC
*/

class StatisticRepository extends BaseRepository
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
        return Statistic::class;
    }
}
