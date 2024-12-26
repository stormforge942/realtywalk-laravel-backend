<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceFormatAndRangeForProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create price format table
        Schema::create('price_formats', function ($table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('name');
            $table->boolean('active')->default(1)->nullable(false);
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable(true);
        });

        //insert initial values
        
        DB::table('price_formats')->insert([
            ['name' => 'Price'],
            ['name' => 'Range'],
            ['name' => 'TBD'],
        ]);

        //link to properties table
        Schema::table('properties', function ($table) {
            $table->decimal('price_to', 10, 2)
                ->after('price')
                ->nullable()
                ->default(null);
            $table->unsignedInteger('price_format_id')->after('levels')->nullable(false)->default(1);
            $table->renameColumn('price', 'price_from');

            $table->foreign('price_format_id')->references('id')->on('price_formats');
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
            $table->dropForeign('properties_price_format_id_foreign');
            $table->renameColumn('price_from', 'price');
            $table->dropColumn('price_to');
            $table->dropColumn('price_format_id');
        });

        Schema::dropIfExists('price_formats');
    }
}
