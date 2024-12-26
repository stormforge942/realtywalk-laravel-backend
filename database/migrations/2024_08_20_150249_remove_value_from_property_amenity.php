<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveValueFromPropertyAmenity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop records with value = 0
        DB::table('property_amenity')->where('value', '0')->delete();

        // Drop column
        Schema::table('property_amenity', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_amenity', function (Blueprint $table) {
            $table->string('value')->nullable();
        });
    }
}
