<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyStylePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_style', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id');
            $table->unsignedInteger('style_id');

            $table->foreign('property_id')
                ->references('id')->on('properties')
                ->onDelete('cascade');

            $table->foreign('style_id')
                ->references('id')->on('styles')
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
        Schema::dropIfExists('property_style');
    }
}
