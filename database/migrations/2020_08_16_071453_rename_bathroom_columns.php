<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameBathroomColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('bathrooms');
            $table->renameColumn('bedrooms_full', 'bathrooms_full');
            $table->renameColumn('bedrooms_half', 'bathrooms_half');
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
            $table->renameColumn('bathrooms_full', 'bedrooms_full');
            $table->renameColumn('bathrooms_half', 'bedrooms_half');
            $table->integer('bathrooms')->nullable();
        });
    }
}
