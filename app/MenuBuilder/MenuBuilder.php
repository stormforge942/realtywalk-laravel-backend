<?php
/*
*   07.11.2019
*   MenuBuilder
*/

namespace App\MenuBuilder;

class MenuBuilder
{
    private $menu;

    private $dropdown;

    private $dropdownDeep;

    public function __construct()
    {
        $this->menu = [];
        $this->dropdown = false;
        $this->dropdownDeep = 0;
    }

    private function innerAddElementToMenuLastPosition(&$menu, $element, $offset)
    {

        $z = 1;
        $result = false;
        $menu = &$menu[count($menu) - 1];
        while (is_array($menu)) {
            if ($z == $this->dropdownDeep - $offset) {
                array_push($menu['elements'], $element);
                $result = true;
                break;
            }
            $menu = &$menu['elements'][count($menu['elements']) - 1];
            $z++;
        }
    }

    private function addElementToMenuLastPosition($element, $offset = 0)
    {
        return $this->innerAddElementToMenuLastPosition($this->menu, $element, $offset);
    }

    private function addRegularLink($id, $name, $href, $icon, $iconType, $sequence = 0, $parent_id = null)
    {

		$hasIcon = ($icon === false || strlen($icon) === 0) ? false : true;
        if ($hasIcon) {
            array_push($this->menu, [
                'id'        => $id,
                'slug'      => 'link',
                'name'      => $name,
                'href'      => $href,
                'hasIcon'   => $hasIcon,
                'icon'      => $icon,
                'iconType'  => $iconType,
                'sequence'  => $sequence,
                'parent_id' => $parent_id,
            ]);
        } else {
            array_push($this->menu, [
                'id'        => $id,
                'slug'      => 'link',
                'name'      => $name,
                'href'      => $href,
                'hasIcon'   => $hasIcon,
                'sequence'  => $sequence,
                'parent_id' => $parent_id,
            ]);
        }
    }

    private function addDropdownLink($id, $name, $href, $icon, $iconType, $sequence = 0, $parent_id = null)
    {
	    $link = explode("/", $href);
        $href = $link[0]."//".$link[2]."/system/".$link[3];
        $extns = array_slice($link,4);
        if(count($extns) > 0){
        	foreach ($extns as $extn) {
            	$href .= "/".$extn;
        	}
        }
        	        //	dd($href);
        $num = count($this->menu);
        $hasIcon = ($icon === false || strlen($icon) === 0) ? false : true;
        if ($hasIcon) {
            $this->addElementToMenuLastPosition([
                'slug'      => 'link',
                'name'      => $name,
                'href'      => $href,
                'hasIcon'   => $hasIcon,
                'icon'      => $icon,
                'iconType'  => $iconType,
                'sequence'  => $sequence,
                'parent_id' => $parent_id,
            ]);
        } else {
            $this->addElementToMenuLastPosition([
                'id'        => $id,
                'slug'      => 'link',
                'name'      => $name,
                'href'      => $href,
                'hasIcon'   => $hasIcon,
                'sequence'  => $sequence,
                'parent_id' => $parent_id,
            ]);
        }
    }

    public function addLink($id, $name, $href, $icon = false, $iconType = 'coreui', $sequence = 0, $parent_id = null)
    {


		if ($this->dropdown === true) {
            $this->addDropdownLink($id, $name, $href, $icon, $iconType, $sequence, $parent_id);
        } else {
            $this->addRegularLink($id, $name, $href, $icon, $iconType, $sequence, $parent_id);
        }
    }

    public function addTitle($id, $name, $icon = false, $iconType = 'coreui', $sequence = 0)
    {
        $hasIcon = ($icon === false || strlen($icon) === 0) ? false : true;
        if ($hasIcon) {
            array_push($this->menu, [
                'id'       => $id,
                'slug'     => 'title',
                'name'     => $name,
                'hasIcon'  => $hasIcon,
                'icon'     => $icon,
                'iconType' => $iconType,
                'sequence' => $sequence,
            ]);
        } else {
            array_push($this->menu, [
                'id'       => $id,
                'slug'     => 'title',
                'name'     => $name,
                'hasIcon'  => $hasIcon,
                'sequence' => $sequence,
            ]);
        }
    }

    public function beginDropdown($id, $name, $icon = false, $iconType = 'coreui', $sequence = 0, $parent_id = null)
    {
        $this->dropdown = true;
        $this->dropdownDeep++;
        $hasIcon = ($icon === false || strlen($icon) === 0) ? false : true;
        if ($this->dropdownDeep === 1) {
            if ($hasIcon) {
                array_push($this->menu, [
                    'id'        => $id,
                    'slug'      => 'dropdown',
                    'name'      => $name,
                    'hasIcon'   => $hasIcon,
                    'icon'      => $icon,
                    'iconType'  => $iconType,
                    'elements'  => [],
                    'sequence'  => $sequence,
                    'parent_id' => $parent_id,
                ]);
            } else {
                array_push($this->menu, [
                    'id'        => $id,
                    'slug'      => 'dropdown',
                    'name'      => $name,
                    'hasIcon'   => $hasIcon,
                    'elements'  => [],
                    'sequence'  => $sequence,
                    'parent_id' => $parent_id,
                ]);
            }
        } else {
            if ($hasIcon) {
                $this->addElementToMenuLastPosition([
                    'id'        => $id,
                    'slug'      => 'dropdown',
                    'name'      => $name,
                    'hasIcon'   => $hasIcon,
                    'icon'      => $icon,
                    'iconType'  => $iconType,
                    'elements'  => [],
                    'sequence'  => $sequence,
                    'parent_id' => $parent_id,
                ], 1);
            } else {
                $this->addElementToMenuLastPosition([
                    'id'        => $id,
                    'slug'      => 'dropdown',
                    'name'      => $name,
                    'hasIcon'   => $hasIcon,
                    'elements'  => [],
                    'sequence'  => $sequence,
                    'parent_id' => $parent_id,
                ], 1);
            }
        }
    }

    public function endDropdown()
    {
        $this->dropdownDeep--;

        if ($this->dropdownDeep < 0) {
            $this->dropdownDeep = 0;
        }
        if ($this->dropdownDeep <= 0) {
            $this->dropdown = false;
        }
    }

    public function getResult()
    {
        return $this->menu;
    }
}
