<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRelationsForSchoolZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->foreignId('school_zone_id')->nullable();
            $table->foreign('school_zone_id')->references('id')->on('school_zones');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('school_zone_id')->nullable();
            $table->foreign('school_zone_id')->references('id')->on('school_zones');
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
            $table->dropForeign(['school_zone_id']);
            $table->dropColumn('school_zone_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['school_zone_id']);
            $table->dropColumn('school_zone_id');
        });
    }
}
