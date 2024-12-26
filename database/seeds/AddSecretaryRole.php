<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSecretaryRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $secretaryRole = DB::table('roles')->where('name', 'secretary')->first()->id ?? null;
        if (!$secretaryRole) {
            $secretaryRole = DB::table('roles')->insertGetId([
                'name' => 'secretary',
                'guard_name' => 'web',
                'updated_at' => now(),
                'created_at' => now()
            ]);
        }

        if (!DB::table('role_hierarchy')->where('role_id', $secretaryRole)->exists()) {
            DB::table('role_hierarchy')->insert([
                'role_id' => $secretaryRole,
                'hierarchy' => 3
            ]);
        }
    }
}
