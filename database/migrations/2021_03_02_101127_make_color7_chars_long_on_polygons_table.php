<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;

class MakeColor7CharsLongOnPolygonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('char')) {
            Type::addType('char', StringType::class);
        }
        Schema::table('polygons', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Type::hasType('char')) {
            Type::addType('char', StringType::class);
        }
        Schema::table('polygons', function (Blueprint $table) {
            $table->char('color', 6)->nullable()->change();
        });
    }
}
