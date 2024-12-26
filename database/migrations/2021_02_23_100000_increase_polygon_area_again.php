<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IncreasePolygonAreaAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polygons', function ($query) {
            $query->decimal('area', 18, 12)->change();
            $query->decimal('min_lat', 18, 12)->change();
            $query->decimal('max_lat', 18, 12)->change();
            $query->decimal('min_lng', 18, 12)->change();
            $query->decimal('max_lng', 18, 12)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
