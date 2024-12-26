<?php

use App\Models\Property;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompoundIndexesToProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index([
                'status',
                'polygon_id',
                'new_construction',
                'request_status',
            ], 'big_filter_wo_polygons_index');

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('big_filter_wo_polygons_index');
            $table->dropIndex('big_filter_w_polygons_index');
        });
    }
}
