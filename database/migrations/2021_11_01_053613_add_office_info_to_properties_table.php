<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeInfoToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('office_id')->nullable()->after('agent_website');
            $table->string('office_name')->nullable()->after('agent_website');
            $table->string('office_website')->nullable()->after('agent_website');
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
            $table->dropColumn([
                'office_website',
                'office_name',
                'office_id',
            ]);
        });
    }
}
