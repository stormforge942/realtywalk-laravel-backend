<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolygonStatisticPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polygon_statistic', function (Blueprint $table) {
            $table->unsignedBigInteger('polygon_id');
            $table->unsignedInteger('statistic_id');
            $table->decimal('value', 10, 2)->nullable();

            $table->foreign('polygon_id')
                ->references('id')->on('polygons')
                ->onDelete('cascade');

            $table->foreign('statistic_id')
                ->references('id')->on('statistics')
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
        Schema::dropIfExists('polygon_statistic');
    }
}
