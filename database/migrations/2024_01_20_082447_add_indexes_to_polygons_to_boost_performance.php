<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToPolygonsToBoostPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->index(['slug', 'title', 'zoom']);
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
            $table->dropIndex(['slug', 'title', 'zoom']);
        });
    }
}
