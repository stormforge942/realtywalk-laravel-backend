<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFloodZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flood_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('zone_id')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones');
            $table->string('flood_zone_ar_id');
            $table->string('flood_zone')->nullable();
            $table->string('flood_zone_subtype')->nullable();
            $table->decimal('area', 10, 8)->nullable();
            $table->decimal('min_lat', 10, 8)->nullable();
            $table->decimal('min_lng', 11, 8)->nullable();
            $table->decimal('max_lat', 10, 8)->nullable();
            $table->decimal('max_lng', 11, 8)->nullable();
            $table->multiPolygon('geometry');
            $table->spatialIndex('geometry');
            $table->json('geometry_json')->nullable();
            $table->char('color', 7)->nullable();
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
        Schema::dropIfExists('flood_zones');
    }
}
