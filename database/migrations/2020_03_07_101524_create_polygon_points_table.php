<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolygonPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polygon_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('polygon_id');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);

            $table->foreign('polygon_id')->references('id')->on('polygons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polygon_points');
    }
}
