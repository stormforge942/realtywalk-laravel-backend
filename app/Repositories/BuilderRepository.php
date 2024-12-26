<?php

namespace App\Repositories;

use App\Models\Builder;
use App\Repositories\BaseRepository;

/**
 * Class BuilderRepository
 * @package App\Repositories
 * @version February 22, 2020, 9:14 am UTC
*/

class BuilderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'slug',
        'email',
        'address1',
        'address2',
        'address3',
        'city',
        'phone',
        'website',
        'hidden'
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
        return Builder::class;
    }
}
