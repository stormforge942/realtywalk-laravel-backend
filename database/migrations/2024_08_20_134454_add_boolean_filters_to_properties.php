<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBooleanFiltersToProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('has_pool')->default(false)->index();
            $table->boolean('has_elevator')->default(false)->index();
            $table->boolean('has_elevator_shaft')->default(false)->index();

            $table->dropIndex('big_filter_w_polygons_index');

            $table->index([
                'new_construction',
                'request_status',
                'polygon_id',
                'status',
                'has_pool',
                'has_elevator',
                'has_elevator_shaft',
                'bedrooms',
                'stories',
                'garage_capacity',
                'bathrooms_full',
                'bathrooms_half',
            ], 'big_filter_w_polygons_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'has_pool',
                'has_elevator',
                'has_elevator_shaft',
            ]);

            $table->dropIndex('big_filter_w_polygons_index');

            $table->index([
                'new_construction',
                'request_status',
                'polygon_id',
                'status',
                'bedrooms',
                'stories',
                'garage_capacity',
                'bathrooms_full',
                'bathrooms_half'
            ], 'big_filter_w_polygons_index');
        });
    }
}
