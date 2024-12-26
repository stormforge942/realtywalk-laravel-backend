<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('mls_number');
            $table->unsignedInteger('builder_id')->nullable();
            $table->unsignedBigInteger('polygon_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();

            $table->string('title', 100);
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('address_number', 50);
            $table->string('address_street');
            $table->string('zipcode', 10);
            $table->text('descr')->nullable();
            $table->char('year_built', 4);
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms_full')->nullable();
            $table->integer('bathrooms_half')->nullable();
            $table->integer('garage_capacity')->nullable();
            $table->integer('sqft')->unsigned()->default(0);
            $table->bigInteger('lot_size')->unsigned()->nullable();
            $table->string('status', 40)->nullable();
            $table->integer('levels')->unsigned()->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('acres', 7, 2)->nullable();
            $table->string('video_embed', 500)->nullable();
            $table->string('finance_type', 40)->nullable();
            $table->boolean('hoa_annual_fee')->nullable();
            $table->timestamps();

            $table->foreign('builder_id')
                ->references('id')->on('builders')
                ->onDelete('set null');

            $table->foreign('polygon_id')
                ->references('id')->on('polygons')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('properties');
    }
}
