<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('slug', 50)->index();
            $table->text('descr')->nullable();
            $table->string('email', 128)->nullable();
            $table->string('address1', 128)->nullable();
            $table->string('address2', 128)->nullable();
            $table->string('address3', 128)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('phone', 12)->nullable();
            $table->string('website', 128)->nullable();
            $table->boolean('hidden')->default(0);
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
        Schema::dropIfExists('builders');
    }
}
