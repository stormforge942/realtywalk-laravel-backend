<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPolygonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->unsignedInteger('zone_id')->nullable()->after('id');
            $table->foreign('zone_id')->references('id')->on('zones');
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
            $table->dropForeign('polygons_zone_id_foreign');
        });
    }
}
