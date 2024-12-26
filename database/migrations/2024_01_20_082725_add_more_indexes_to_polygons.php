<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreIndexesToPolygons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polygons', function (Blueprint $table) {
            $table->dropIndex(['slug', 'title', 'zoom']);
            $table->index('slug');
            $table->index('title');
            $table->index('zoom');
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
            $table->index(['slug', 'title', 'zoom']);
            $table->dropIndex('slug');
            $table->dropIndex('title');
            $table->dropIndex('zoom');
        });
    }
}
