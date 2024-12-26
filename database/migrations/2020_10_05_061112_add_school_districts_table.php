<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolDistrictsTable extends Migration
{
    private $cols = [
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

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_districts', function (Blueprint $table) {
            $table->string('ext_id')->unique();

            foreach ($this->cols as $col) {
                $table->string($col);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_districts');
    }
}
