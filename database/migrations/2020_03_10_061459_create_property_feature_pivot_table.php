<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyFeaturePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_amenity', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id');
            $table->unsignedInteger('amenity_id');
            $table->string('value')->nullable();

            $table->foreign('property_id')
                  ->references('id')->on('properties')
                  ->onDelete('cascade');

            $table->foreign('amenity_id')
                  ->references('id')->on('amenities')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_amenity');
    }
}
