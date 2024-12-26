<?php

namespace App\MenuBuilder;

use App\Models\Menu;

class SidebarMenu implements MenuInterface
{

    private $mb;

    private $menu;

    public function __construct()
    {
        $this->mb = new MenuBuilder;
    }

    private function getMenuFromDB($menuId, $menuName)
    {
        $this->menu = Menu::join('menu_role', 'menus.id', '=', 'menu_role.menus_id')
            ->select('menus.*')
            ->where('menus.menu_id', '=', $menuId)
            ->where('menu_role.role_name', '=', $menuName)
            ->orderBy('menus.sequence', 'asc')
            ->get();
    }

    private function getGuestMenu($menuId)
    {
        $this->getMenuFromDB($menuId, 'guest');
    }

    private function getUserMenu($menuId)
    {
        $this->getMenuFromDB($menuId, 'user');
    }

    private function getAdminMenu($menuId)
    {
        $this->getMenuFromDB($menuId, 'admin');
    }

    public function get($role, $menuId = 2)
    {
        $this->getMenuFromDB($menuId, $role);
        $rfd = new RenderFromDatabaseData;

        return $rfd->render($this->menu);
    }

    public function getAll($menuId = 2)
    {
        $this->menu = Menu::select('menus.*')
            ->where('menus.menu_id', '=', $menuId)
            ->orderBy('menus.sequence', 'asc')
            ->get();

        $rfd = new RenderFromDatabaseData;

        return $rfd->render($this->menu);
    }
}
