<?php

namespace App\Repositories;

use App\Models\UserSearches;
use App\Repositories\BaseRepository;

/**
 * Class PolygonRepository
 * @package App\Repositories
 * @version March 5, 2020, 12:40 pm UTC
*/

class UserSearchesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'search_name',
        'search_string_criteria'
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
        return UserSearches::class;
    }
}
