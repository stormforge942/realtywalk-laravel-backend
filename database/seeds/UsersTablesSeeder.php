<?php

use App\Models\RoleHierarchy;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::create(['name' => 'admin']);
        RoleHierarchy::create([
            'role_id'   => $admin->id,
            'hierarchy' => 1
        ]);

        $user = Role::create(['name' => 'user']);
        RoleHierarchy::create([
            'role_id'   => $user->id,
            'hierarchy' => 2
        ]);

        $builder = Role::create(['name' => 'builder']);
        RoleHierarchy::create([
            'role_id'   => $builder->id,
            'hierarchy' => 2
        ]);

        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@newhomewalk.com',
            'password' => 'admin1234',
        ])->assignRole('admin');

        User::create([
            'name'     => 'Yefta AW',
            'email'    => 'hello@yefta.me',
            'password' => 'admin1234',
        ])->assignRole('admin');

        User::create([
            'name'     => 'Gal Doron',
            'email'    => 'galdg@gmail.com',
            'password' => 'admin1234',
        ])->assignRole('admin');

        User::create([
            'name'          => 'John Doe',
            'email'         => 'john.doe@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-02-12 04:12:00'
        ])->assignRole('user');

        User::create([
            'name'          => 'Friderik Dávid',
            'email'         => 'friederik@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-02-20 02:13:50'
        ])->assignRole('user');

        User::create([
            'name'          => 'Alexandre Hleb',
            'email'         => 'hleb@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-02-21 09:15:32'
        ])->assignRole('user');

        User::create([
            'name'          => 'Alfred Stevenson',
            'email'         => 'alfred.s@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-02-25 10:56:02'
        ])->assignRole('user');

        User::create([
            'name'          => 'Gareth Liam',
            'email'         => 'liam.g@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-03-01 00:45:20'
        ])->assignRole('user');

        User::create([
            'name'          => 'Daeng Sutigna',
            'email'         => 'daeng.sutigna@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-03-02 02:00:14'
        ])->assignRole('user');

        User::create([
            'name'          => 'Zee Dewa',
            'email'         => 'zee.dewa@example.com',
            'password'      => 'user1234',
            'last_login_at' => '2020-03-05 12:12:12'
        ])->assignRole('user');

        User::create([
            'name'          => 'User Builder',
            'email'         => 'yefta@nytrobit.io',
            'password'      => 'builder1234',
            'last_login_at' => '2020-03-05 10:00:50',
            'builder_id'    => 4
        ])->assignRole('builder');
    }
}
