<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolDistrict extends Model
{
    protected $fillable = [
        'ext_id',
        "district_name",
        "school_url",
        "grade_low",
        "grade_high",
        "school_cnt",
        "enrollment",
        "urban_centric_locale_type",
        "urban_centric_community_type",
        "total_per_pupil_expenditure_amt",
        "per_pupil_exp_instr_pct",
        "total_expenditure_amt",
        "charter_school_cnt",
        "magnet_school_cnt",
        "pop_age_5_17_below_poverty_level_pct",
        "school_district_rating"
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'school_district_id', 'ext_id');
    }
}
