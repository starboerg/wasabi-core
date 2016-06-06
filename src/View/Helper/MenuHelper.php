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
 * @property HtmlHelper $Html
 */
class MenuHelper extends Helper
{
    /**
     * Helpers used by this helper
     *
     * @var array
     */
    public $helpers = [
        'Html' => [
            'className' => 'Wasabi/Core.Html'
        ],
        'Guardian' => [
            'className' => 'Wasabi/Core.Guardian'
        ]
    ];

    /**
     * Render provided $items as <li> elements
     *
     * @param array $items The menu items to render.
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
     * @param array $items The menu items to render.
     * @param string $activeClass Active css class that is applied to active items.
     * @param string $openClass Open class that is applied to a parent menu item when children are active.
     * @param string $subNavClass Css class applied to any child ul of a parent menu item.
     * @return string The rendered items without an outer ul.
     */
    public function renderNested(array $items, $activeClass = 'active', $openClass = 'open', $subNavClass = 'sub-nav collapse')
    {
        $out = '';
        foreach ($items as $item) {
            $subNavActiveClass = '';
            $cls = $this->_buildClasses($item, $activeClass, $openClass, $subNavActiveClass);

            $itemOut = '<li' . ((count($cls) > 0) ? ' class="' . join(' ', $cls) . '"' : '') . '>';

            $itemLink = $this->_renderItemLink($item);
            if ($itemLink === '') {
                continue;
            }

            $itemOut .= $itemLink;
            $itemOut .= $this->_renderChildren($item, $subNavClass, $subNavActiveClass);
            $itemOut .= '</li>';

            $out .= $itemOut;
        }

        return $out;
    }

    /**
     * Used to render menu items as a ul li fake table
     * in the backend.
     *
     * @param array $menuItems The menu items to render as tree.
     * @param null|int $level The current level.
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

    /**
     * Build the css classes for a menu item.
     *
     * @param array $item The menu item.
     * @param string $activeClass The active css class.
     * @param string $openClass The open css class.
     * @param string $subNavActiveClass The sub navigation active css class.
     * @return array An array of applied css classes.
     */
    protected function _buildClasses($item, $activeClass, $openClass, &$subNavActiveClass)
    {
        $cls = [];

        if ($this->_isActive($item)) {
            $cls[] = $activeClass;
        }

        if ($this->_isOpen($item)) {
            $cls[] = $openClass;
            $subNavActiveClass .= ' in';
        }

        return $cls;
    }

    /**
     * Check if the menu item is active.
     *
     * @param array $item The menu item.
     * @return bool
     */
    protected function _isActive($item)
    {
        return (isset($item['active']) && $item['active'] === true);
    }

    /**
     * Check if the menu item is open.
     *
     * @param array $item The menu item.
     * @return bool
     */
    protected function _isOpen($item)
    {
        return (isset($item['open']) && $item['open'] === true);
    }

    /**
     * Check if the menu item has children.
     *
     * @param array $item The menu item.
     * @return bool
     */
    protected function _hasChildren($item)
    {
        return (isset($item['children']) && !empty($item['children']));
    }

    /**
     * Render the icon of the menu item.
     *
     * @param array $item The menu item.
     * @return string
     */
    protected function _renderIcon($item)
    {
        if (!isset($item['icon'])) {
            return '';
        }
        return '<i class="' . $item['icon'] . '"></i>';
    }

    /**
     * Render the name of the menu item.
     *
     * @param array $item The menu item.
     * @return string
     */
    protected function _renderName($item)
    {
        return '<span class="item-name">' . $item['name'] . '</span>';
    }

    /**
     * Render the item link of the menu item.
     *
     * @param array $item The menu item.
     * @return string
     */
    protected function _renderItemLink($item)
    {
        $linkText = $this->_renderIcon($item) . $this->_renderName($item);
        $options = $item['linkOptions'] ?? [];
        $options['escape'] = false;

        if (isset($item['url'])) {
            $itemLink = $this->Guardian->protectedLink($linkText, $item['url'], $options);
        } else {
            if ($this->_hasChildren($item)) {
                $linkText .= ' <i class="icon-arrow-left"></i>';
                $linkText .= ' <i class="icon-arrow-down"></i>';
            }
            $itemLink = $this->Html->link($linkText, 'javascript:void(0)', $options);
        }
        return $itemLink;
    }

    /**
     * Render the subnavigation of the menu item.
     *
     * @param array $item The menu item.
     * @param string $subNavClass The subnavigation class.
     * @param string $subNavActiveClass The subnavigation active class.
     * @return string
     */
    protected function _renderChildren($item, $subNavClass, $subNavActiveClass)
    {
        if (!$this->_hasChildren($item)) {
            return '';
        }

        $nestedOut = $this->renderNested($item['children']);
        if ($nestedOut === '') {
            return '';
        }

        return '<ul class="' . $subNavClass . $subNavActiveClass . '">' . $nestedOut . '</ul>';
    }
}
