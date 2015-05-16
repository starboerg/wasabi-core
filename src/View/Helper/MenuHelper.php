<?php
/**
 * Wasabi CMS
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Helper;

use Cake\View\Helper;

/**
 * Class MenuHelper
 *
 * @property GuardianHelper $Guardian
 */
class MenuHelper extends Helper
{

    /**
     * Helpers used by this helper
     *
     * @var array
     */
    public $helpers = array(
        'Guardian' => array(
            'className' => 'Wasabi/Core.Guardian'
        )
    );

    /**
     * Render provided $items as <li> elements
     *
     * @param array $items
     * @param string $activeClass css class to add to active items
     * @return string
     */
    public function render($items, $activeClass = 'active')
    {
        $out = '';
        foreach ($items as $item) {
            $class = '';
            if (isset($item['active']) && $item['active'] === true) {
                $class = ' class="' . $activeClass . '"';
            }
            $out .= '<li' . $class . '>';
            $out .= $this->Guardian->protectedLink($item['name'], $item['url']);
            $out .= '</li>';
        }
        return $out;
    }

    /**
     * Render all nested items of a menu.
     *
     * @param array $items items to render
     * @param string $activeClass active css class that is applied to active items
     * @param string $openClass open class that is applied to a parent menu item when children are active
     * @param string $subNavClass css class applied to any child ul of a parent menu item
     * @return string the rendered items without an outer ul
     */
    public function renderNested(array $items, $activeClass = 'active', $openClass = 'open', $subNavClass = 'sub-nav')
    {
        $out = '';
        foreach ($items as $item) {
            $cls = [];
            if (isset($item['active']) && $item['active'] === true) {
                $cls[] = $activeClass;
            }
            if (isset($item['open']) && $item['open'] === true) {
                $cls[] = $openClass;
            }
            $out .= '<li' . ((count($cls) > 0) ? ' class="' . join(' ', $cls) . '"' : '') . '>';
            if (isset($item['url'])) {
                $options = [];
                if (isset($item['icon'])) {
                    $item['name'] = '<i class="' . $item['icon'] . '"></i><span class="item-name">' . $item['name'] . '</span>';
                    $options['escape'] = false;
                }
                $out .= $this->Guardian->protectedLink($item['name'], $item['url'], $options);
            } else {
                $out .= '<a href="javascript:void(0)">';
                if (isset($item['icon'])) {
                    $out .= '<i class="' . $item['icon'] . '"></i>';
                }
                $out .= '<span class="item-name">' . $item['name'] . '</span>';
                if (isset($item['children']) && !empty($item['children'])) {
                    $out .= ' <i class="icon-arrow-down"></i>';
                    $out .= ' <i class="icon-arrow-up"></i>';
                }
                $out .= '</a>';
            }
            if (isset($item['children']) && !empty($item['children'])) {
                $out .= '<ul class="' . $subNavClass . '">';
                $out .= $this->renderNested($item['children']);
                $out .= '</ul>';
            }
            $out .= '</li>';
        }

        return $out;
    }

    /**
     * Used to render menu items as a ul li fake table
     * in the backend.
     *
     * @param $menuItems
     * @param null $level
     * @return string
     */
    public function renderTree($menuItems, $level = null)
    {
        $output = '';

        $depth = ($level !== null) ? $level : 1;

        foreach ($menuItems as $key => $menuItem) {
            $classes = ['menu-item'];
            $menuItemRow = $this->_View->element('../Menus/__menu-item-row', [
                'menuItem' => $menuItem,
                'level' => $level
            ]);

            if (!empty($menuItem['children'])) {
                $menuItemRow .= '<ul>' . $this->renderTree($menuItem['children'], $depth + 1) . '</ul>';
            } else {
                $classes[] = 'no-children';
            }
            $output .= '<li class="' . join(' ', $classes) . '" data-menu-item-id="' . $menuItem['id'] . '">' . $menuItemRow . '</li>';
        }

        return $output;
    }
}
