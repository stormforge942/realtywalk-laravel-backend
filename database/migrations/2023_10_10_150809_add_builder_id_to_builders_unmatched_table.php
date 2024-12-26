<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuilderIdToBuildersUnmatchedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('builders_unmatched', function (Blueprint $table) {
            $table->unsignedInteger('builder_id')->nullable()->after('name');

            $table->foreign('builder_id')
                ->references('id')->on('builders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('builders_unmatched', function (Blueprint $table) {
            $table->dropForeign(['builder_id']);
            $table->dropColumn('builder_id');
        });
    }
}
