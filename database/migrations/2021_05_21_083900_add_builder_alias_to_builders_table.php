<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuilderAliasToBuildersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('builders', function (Blueprint $table) {
            $table->unsignedInteger('alias_of_builder_id')->nullable();

            $table->foreign('alias_of_builder_id')
                  ->references('id')->on('builders')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('builders', function (Blueprint $table) {
            $table->dropForeign('builders_alias_of_builder_id_foreign');
        });
        Schema::table('builders', function (Blueprint $table) {
            $table->dropColumn('alias_of_builder_id');
        });
    }
}
