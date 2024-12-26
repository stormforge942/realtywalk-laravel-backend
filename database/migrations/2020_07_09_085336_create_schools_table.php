<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id()->comment = "Auto Increment";
            $table->string("extId")->nullable()->comment = "File external ID";
            $table->string('state_school_id')->nullable()->comment = "State School ID";
            $table->string('school_district_id')->nullable()->comment= "School district ID";
            $table->string('name')->nullable()->comment = "School Name";
            $table->string('address')->nullable()->comment = "School Address";
            $table->string('phone_number')->nullable()->comment = "Phone Number";
            $table->string('school_url')->nullable()->comment= "School URL";
            $table->string("latitude")->comment = "Latitude";
            $table->string("longitude")->comment = "Longitude";
            $table->bigInteger('polygon_id')->comment = "Polygon Id";
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools');
    }
}
