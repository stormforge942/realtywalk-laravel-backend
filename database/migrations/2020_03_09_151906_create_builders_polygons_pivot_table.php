<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildersPolygonsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builder_polygon', function (Blueprint $table) {
            $table->unsignedInteger('builder_id');
            $table->unsignedBigInteger('polygon_id');

            $table->foreign('builder_id')
                  ->references('id')->on('builders')
                  ->onDelete('cascade');

            $table->foreign('polygon_id')
                  ->references('id')->on('polygons')
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
        Schema::dropIfExists('builder_polygon');
    }
}
