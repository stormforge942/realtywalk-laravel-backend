<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFullAddressStoredAsFormatPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function ($table) {
            $table->dropColumn('full_address');
        });

        Schema::table('properties', function ($table) {
            $table->string('full_address')
                ->default(null)
                ->nullable(true)
                ->storedAs("(CONCAT(`address_number`, ' ', `address_street`))");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function ($table) {
            $table->dropColumn('full_address');
        });

        Schema::table('properties', function($table) {
            $table->string('full_address')
                ->default(null)
                ->nullable(true)
                ->storedAs("(CONCAT(`address_number`, ', ', `address_street`))");
        });
    }
}