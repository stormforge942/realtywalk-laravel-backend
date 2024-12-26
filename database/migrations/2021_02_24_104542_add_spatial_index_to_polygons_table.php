<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSpatialIndexToPolygonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('polygons')->whereNull('geometry')->delete();
        Schema::table('polygons', function (Blueprint $table) {
            $table->multiPolygon('geometry')->nullable(false)->change();
            $table->spatialIndex('geometry');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->dropSpatialIndex('polygons_geometry_spatial');
        });
        Schema::table('polygons', function (Blueprint $table) {
            $table->multiPolygon('geometry')->nullable(true)->change();
        });
    }
}
