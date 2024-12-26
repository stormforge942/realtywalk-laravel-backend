<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsOfInterest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points_of_interests', function (Blueprint $table) {
            $table->id();
            $table->string('extId')->nullable();
            $table->string('name')->nullable()->comment('Name of point of interest');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('state_fips_cd')->nullable();
            $table->string('county_fips_cd')->nullable();
            $table->string('phone')->nullable();
            $table->string('longitude');
            $table->string('latitude');
            $table->string('polygon_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('points_of_interest');
    }
}
