<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index('type');
            $table->index('request_status');
            $table->index('status');
            $table->index('price_from');
            $table->index('price_to');
            $table->index('bedrooms');
            $table->index('bathrooms_full');
            $table->index('bathrooms_half');
            $table->index('garage_capacity');
            $table->index('sqft');
            $table->index('lot_size');
            $table->index('stories');
            $table->index('new_construction');

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->onDelete('set null');
        });

        Schema::table('amenities', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('property_amenity', function (Blueprint $table) {
            $table->index('value');
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
            $table->dropIndex(['type']);
            $table->dropIndex(['request_status']);
            $table->dropIndex(['status']);
            $table->dropIndex(['price_from']);
            $table->dropIndex(['price_to']);
            $table->dropIndex(['bedrooms']);
            $table->dropIndex(['bathrooms_full']);
            $table->dropIndex(['bathrooms_half']);
            $table->dropIndex(['garage_capacity']);
            $table->dropIndex(['sqft']);
            $table->dropIndex(['lot_size']);
            $table->dropIndex(['stories']);
            $table->dropIndex(['new_construction']);

            $table->dropForeign(['category_id']);
        });

        Schema::table('amenities', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('property_amenity', function (Blueprint $table) {
            $table->dropIndex(['value']);
        });
    }
}
