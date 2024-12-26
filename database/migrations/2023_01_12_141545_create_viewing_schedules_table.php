<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewingSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viewing_schedules', function (Blueprint $table) {
            $table->id();
            $table->dateTime('datetime');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('name');
            $table->text('message');
            $table->string('phone_number');
            $table->string('email')->index();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('property_id')
                ->references('id')->on('properties')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('viewing_schedules');
    }
}
