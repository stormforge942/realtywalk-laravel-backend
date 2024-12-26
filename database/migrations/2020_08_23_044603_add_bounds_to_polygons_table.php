<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBoundsToPolygonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->decimal('min_lat', 10, 8)->nullable()->after('zoom');
            $table->decimal('min_lng', 11, 8)->nullable()->after('min_lat');
            $table->decimal('max_lat', 10, 8)->nullable()->after('min_lng');
            $table->decimal('max_lng', 11, 8)->nullable()->after('max_lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->dropColumn('min_lat');
            $table->dropColumn('min_lng');
            $table->dropColumn('max_lat');
            $table->dropColumn('max_lng');
        });
    }
}
