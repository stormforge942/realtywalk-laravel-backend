<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddEmailVerifiedAtToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_token', 64)->nullable()->after('email_verified_at');
            $table->string('password')->nullable()->change();
        });

        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => DB::raw('created_at')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_token');
        });

        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => null
        ]);
    }
}
