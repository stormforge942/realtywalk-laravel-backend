<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecretaryToRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!DB::table('roles')->where('name', 'secretary')->count()) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'secretary',
                'guard_name' => 'web',
                'updated_at' => now(),
                'created_at' => now()
            ]);
        }
        
        if (!DB::table('role_hierarchy')->where('role_id', $roleId)->count()) {
            DB::table('role_hierarchy')->insert([
                'role_id' => $roleId,
                'hierarchy' => 3
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = DB::table('roles')->where('name', 'secretary');

        if ($role) {
            $roleId = $role->first()->id;

            DB::table('role_hierarchy')
                ->where('role_id', $roleId)
                ->delete();

            $role->delete();
        }    
    }
}
