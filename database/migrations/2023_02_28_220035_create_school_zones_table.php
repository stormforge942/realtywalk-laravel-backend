<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('zone_id')->nullable();
            $table->foreign('zone_id')->references('id')->on('zones');
            $table->string('title');
            $table->string('title_short', 100)->nullable();
            $table->enum('type', ['elementary', 'highschool', 'middleschool'])->nullable();
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
        Schema::dropIfExists('school_zones');
    }
}
