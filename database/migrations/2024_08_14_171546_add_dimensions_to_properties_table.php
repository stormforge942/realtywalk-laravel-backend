<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDimensionsToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('lot_right_dimension', 7, 2)->nullable()->after('lot_size');
            $table->decimal('lot_left_dimension', 7, 2)->nullable()->after('lot_size');
            $table->decimal('lot_back_dimension', 7, 2)->nullable()->after('lot_size');
            $table->decimal('lot_front_dimension', 7, 2)->nullable()->after('lot_size');
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
                'lot_front_dimension',
                'lot_back_dimension',
                'lot_left_dimension',
                'lot_right_dimension',
            ]);
        });
    }
}
