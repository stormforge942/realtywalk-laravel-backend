<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSchoolFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('district_name')->nullable()->after('phone_number');
            $table->string('school_type')->nullable()->after('district_name');
            $table->string('instructional_level')->nullable()->after('school_type');
            $table->string('grade_low')->nullable()->after('instructional_level');
            $table->string('grade_high')->nullable()->after('grade_low');
            $table->boolean('elementary_school_ind')->nullable()->after('grade_high');
            $table->boolean('middle_school_ind')->nullable()->after('elementary_school_ind');
            $table->boolean('high_school_ind')->nullable()->after('middle_school_ind');
            $table->boolean('ee_ind')->nullable()->after('high_school_ind');
            $table->boolean('pk_ind')->nullable()->after('ee_ind');
            $table->boolean('kg_ind')->nullable()->after('pk_ind');
            $table->string('enrollment')->nullable()->after('kg_ind');
            $table->string('student_teacher_ratio')->nullable()->after('kg_ind');
            $table->string('title_i_schoolwide_program_eligible_ind')->nullable()->after('kg_ind');
            $table->boolean('virtual_school_ind')->nullable();
            $table->boolean('magnet_ind')->nullable();
            $table->boolean('charter_ind')->nullable();
            $table->boolean('kg_full_day_ind')->nullable();
            $table->boolean('kg_half_day_ind')->nullable();
            $table->string('catholic_school_type')->nullable();
            $table->string('private_school_orientation')->nullable();
            $table->boolean('bilingual_schools_assoc_ind')->nullable();
            $table->string('enroll_am')->nullable();
            $table->string('enroll_asian')->nullable();
            $table->string('enroll_hisp')->nullable();
            $table->string('enroll_black')->nullable();
            $table->string('enroll_white')->nullable();
            $table->string('enroll_pacific')->nullable();
            $table->string('enroll_multiple_races')->nullable();
            $table->string('enroll_am_pct')->nullable();
            $table->string('enroll_male')->nullable();
            $table->string('enroll_female')->nullable();
            $table->boolean('ap_ind')->nullable();
            $table->boolean('before_and_after_school_prog_ind')->nullable();
            $table->boolean('gifted_and_talented_prog_ind')->nullable();
            $table->boolean('bilingual_education_ind')->nullable();
            $table->string('enrollment_change_cnt')->nullable();
            $table->string('school_rating')->nullable();

            $table->bigInteger('polygon_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('district_name');
            $table->dropColumn('school_type');
            $table->dropColumn('instructional_level');
            $table->dropColumn('grade_low');
            $table->dropColumn('grade_high');
            $table->dropColumn('elementary_school_ind');
            $table->dropColumn('middle_school_ind');
            $table->dropColumn('high_school_ind');
            $table->dropColumn('ee_ind');
            $table->dropColumn('pk_ind');
            $table->dropColumn('kg_ind');

            $table->bigInteger('polygon_id')->change();
            $cols = [
                'enrollment',
                'student_teacher_ratio',
                'title_i_schoolwide_program_eligible_ind',
                'virtual_school_ind',
                'magnet_ind',
                'charter_ind',
                'kg_full_day_ind',
                'kg_half_day_ind',
                'catholic_school_type',
                'private_school_orientation',
                'bilingual_schools_assoc_ind',
                'enroll_am',
                'enroll_asian',
                'enroll_hisp',
                'enroll_black',
                'enroll_white',
                'enroll_pacific',
                'enroll_multiple_races',
                'enroll_am_pct',
                'enroll_male',
                'enroll_female',
                'ap_ind',
                'before_and_after_school_prog_ind',
                'gifted_and_talented_prog_ind',
                'bilingual_education_ind',
                'enrollment_change_cnt',
                'school_rating',
            ];
            foreach ($cols as $col) {
                $table->dropColumn($col);
            }
        });
    }
}
