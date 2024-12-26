<?php

namespace App\Repositories;

use App\Models\Style;
use App\Repositories\BaseRepository;

/**
 * Class StyleRepository
 * @package App\Repositories
 * @version March 11, 2020, 8:13 am UTC
*/

class StyleRepository extends BaseRepository
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
        return Style::class;
    }
}
