<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadLockingColumnToAllAvailableMediaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('is_uploading_files')->default(false);
        });

        Schema::table('polygons', function (Blueprint $table) {
            $table->boolean('is_uploading_files')->default(false)->after('display_as_background');
        });

        Schema::table('builders', function (Blueprint $table) {
            $table->boolean('is_uploading_files')->default(false)->after('hidden');
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
            $table->dropColumn('is_uploading_files');
        });

        Schema::table('polygons', function (Blueprint $table) {
            $table->dropColumn('is_uploading_files');
        });

        Schema::table('builders', function (Blueprint $table) {
            $table->dropColumn('is_uploading_files');
        });
    }
}
