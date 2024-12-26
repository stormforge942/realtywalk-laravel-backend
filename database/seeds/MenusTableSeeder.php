<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MenusTableSeeder extends Seeder
{
    private $menuId = null;
    private $dropdownId = array();
    private $dropdown = false;
    private $sequence = 1;
    private $joinData = array();
    private $adminRole = null;
    private $userRole = null;
    private $subFolder = '';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Set subfolder
         * If your url looks like: example.org/sub-folder
         * then:
         * $this->subFolder = '/sub-folder';
        */
        $this->subFolder = config('app.url');

        if (substr($this->subFolder, -1) == '/') {
            $this->subFolder = rtrim($this->subFolder, '/');
        }

        /* Get roles */
        $this->adminRole = Role::whereName('admin')->first();
        $this->userRole = Role::whereName('user')->first();

        /* Create Sidebar menu */
        DB::table('menu_list')->insert([
            'name' => 'sidebar menu'
        ]);

        // Set menuId
        $this->menuId = DB::getPdo()->lastInsertId();

        /* A. Dashboard */
        $this->insertLink('user,admin', 'Dashboard', '/', 'cil-speedometer');

        /* B. Main Menu */
        $this->insertTitle('user,admin,secretary', 'Main Menu');

        /** 1. Properties */
        $this->beginDropdown('admin,secretary', 'Properties', 'cil-building');
        $this->insertLink('admin,secretary', 'List', '/properties');
        $this->insertLink('admin,secretary', 'Add New', '/properties/create');
        $this->insertLink('admin', 'Categories', '/properties/categories');
        $this->insertLink('admin', 'Amenities', '/properties/amenities');
        $this->insertLink('admin', 'Styles', '/properties/styles');
        $this->endDropdown();

        /** 2. Builders */
        $this->beginDropdown('admin,secretary', 'Builders', 'cil-briefcase');
        $this->insertLink('admin,secretary', 'List', '/builders');
        $this->insertLink('admin,secretary', 'Add New', '/builders/create');
        $this->endDropdown();

        /** 3. Polygons */
        $this->beginDropdown('admin', 'Polygons', 'cil-camera-control');
        $this->insertLink('admin', 'List', '/polygons');
        $this->insertLink('admin', 'Add New', '/polygons/create');
        $this->insertLink('admin', 'Zones', '/polygons/zones');
        $this->insertLink('admin', 'Statistics', '/polygons/statistics');
        $this->endDropdown();

        /** 4. Users */
        $this->beginDropdown('admin', 'Users', 'cil-people');
        $this->insertLink('admin', 'List', '/users');
        $this->insertLink('admin', 'Add New', '/users/create');
        $this->endDropdown();

        /** 5. Settings */
        $this->beginDropdown('admin', 'Settings', 'cil-settings');
        $this->insertLink('admin', 'General', '/settings');
        $this->insertLink('admin', 'Email', '/settings/email');
        $this->insertLink('admin', 'Builder', '/settings/builder');
        $this->endDropdown();

        /* Create top menu */
        DB::table('menu_list')->insert([
            'name' => 'top menu'
        ]);

        // Set menuId
        $this->menuId = DB::getPdo()->lastInsertId();

        /* Must by use on end of this seeder */
        $this->joinAllByTransaction();
    }

    public function join($roles, $menusId)
    {
        $roles = explode(',', $roles);

        foreach ($roles as $role) {
            array_push($this->joinData, [
                'role_name' => $role,
                'menus_id'  => $menusId
            ]);
        }
    }

    /**
     * Function assigns menu elements to roles
     * Must by use on end of this seeder
     */
    public function joinAllByTransaction()
    {
        DB::beginTransaction();

        foreach ($this->joinData as $data) {
            DB::table('menu_role')->insert([
                'role_name' => $data['role_name'],
                'menus_id'  => $data['menus_id'],
            ]);
        }

        DB::commit();
    }

    public function insertLink($roles, $name, $href, $icon = null)
    {
        $href = $this->subFolder . $href;

        if ($this->dropdown === false) {
            DB::table('menus')->insert([
                'slug'     => 'link',
                'name'     => $name,
                'icon'     => $icon,
                'href'     => $href,
                'menu_id'  => $this->menuId,
                'sequence' => $this->sequence
            ]);
        } else {
            DB::table('menus')->insert([
                'slug'      => 'link',
                'name'      => $name,
                'icon'      => $icon,
                'href'      => $href,
                'menu_id'   => $this->menuId,
                'parent_id' => $this->dropdownId[count($this->dropdownId) - 1],
                'sequence'  => $this->sequence
            ]);
        }

        $this->sequence++;
        $lastId = DB::getPdo()->lastInsertId();
        $this->join($roles, $lastId);
        $permission = Permission::where('name', '=', $name)->get();

        if (empty($permission)) {
            $permission = Permission::create(['name' => 'visit ' . $name]);
        }
        $roles = explode(',', $roles);

        if (in_array('user', $roles)) {
            $this->userRole->givePermissionTo($permission);
        }

        if (in_array('admin', $roles)) {
            $this->adminRole->givePermissionTo($permission);
        }

        return $lastId;
    }

    public function insertTitle($roles, $name)
    {
        DB::table('menus')->insert([
            'slug'     => 'title',
            'name'     => $name,
            'menu_id'  => $this->menuId,
            'sequence' => $this->sequence
        ]);

        $this->sequence++;
        $lastId = DB::getPdo()->lastInsertId();
        $this->join($roles, $lastId);

        return $lastId;
    }

    public function beginDropdown($roles, $name, $icon = '')
    {
        if (count($this->dropdownId)) {
            $parentId = $this->dropdownId[count($this->dropdownId) - 1];
        } else {
            $parentId = null;
        }

        DB::table('menus')->insert([
            'slug'      => 'dropdown',
            'name'      => $name,
            'icon'      => $icon,
            'menu_id'   => $this->menuId,
            'sequence'  => $this->sequence,
            'parent_id' => $parentId
        ]);

        $lastId = DB::getPdo()->lastInsertId();
        array_push($this->dropdownId, $lastId);
        $this->dropdown = true;
        $this->sequence++;
        $this->join($roles, $lastId);

        return $lastId;
    }

    public function endDropdown()
    {
        $this->dropdown = false;

        array_pop($this->dropdownId);
    }
}
