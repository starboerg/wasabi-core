<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\View\Helper;

use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * Class MenuHelper
 *
 * @property GuardianHelper $Guardian
 * @property HtmlHelper $Html
 */
class MenuHelper extends Helper
{
    use StringTemplateTrait;

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
     * Default config for the helper.
     *
     * @var array
     */
    protected $_defaultConfig = [
        // Css class of a single menu item (li).
        'itemClass' => 'menu--item',
        // Active css class that is applied to active items.
        'itemActiveClass' => 'active',
        // Open class that is applied to a parent menu item when children are active.
        'itemOpenClass' => 'open',
        // Css class applied to any child ul of a parent menu item.
        'subnavClass' => 'menu--item-subnav level-{{level}} collapse',
        // The additional css class to add to open sub navigation uls.
        'subnavOpenClass' => 'in',
        // Css class for menu item links (a).
        'linkClass' => 'menu--item-link',
        // Css class applied to nested links in sub menus.
        'nestedLinkClass' => 'is-nested',
        // Css class used for menu item names.
        'nameClass' => 'menu--item-name',
        // Css class used for menu item short names.
        'shortNameClass' => 'menu--item-short-name',
        // Css class used for the icon of a menu item.
        'iconClass' => 'menu--item-icon',
        // Css class used for carets to mark menu items containing sub menus.
        'caretClass' => 'menu--item-caret',
        // All templates used for menu rendering.
        'templates' => [
            'item' => '<li{{attrs}}>{{link}}{{subnav}}</li>',
            'subnav' => '<ul{{attrs}}>{{items}}</ul>',
            'linkContent' => '{{icon}}{{shortName}}{{name}}{{caret}}',
            'icon' => '<i{{attrs}}></i>',
            'shortName' => '<span{{attrs}}>{{shortName}}</span>',
            'name' => '<span{{attrs}}>{{name}}</span>',
            'caret' => '<i{{attrs}}></i>',
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
     * @param int $level The current level of rendered menu items.
     * @return string The rendered items without an outer ul.
     */
    public function renderNested(array $items, $level = 0)
    {
        $out = '';
        foreach ($items as $item) {
            $out .= $this->_renderItem($item, $level);
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
     * @return array An array of applied css classes.
     */
    protected function _buildItemClasses($item)
    {
        $cls = [
            $this->config('itemClass')
        ];

        if ($this->_isActive($item)) {
            $cls[] = $this->config('itemActiveClass');
        }

        if ($this->_isOpen($item)) {
            $cls[] = $this->config('itemOpenClass');
        }

        return $cls;
    }

    protected function _buildLinkClasses($level)
    {
        $cls = [
            $this->config('linkClass')
        ];

        if ($level > 0) {
            $cls[] = $this->config('nestedLinkClass');
        }

        return $cls;
    }

    protected function _buildSubnavClasses($item, $level)
    {
        $cls = [
            str_replace('{{level}}', (string)$level, $this->config('subnavClass'))
        ];

        if ($this->_isOpen($item)) {
            $cls[] = $this->config('subnavOpenClass');
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

    protected function _renderItem($item, $level)
    {
        if (($itemLink = $this->_renderLink($item, $level)) === '') {
            return '';
        }

        $cls = $this->_buildItemClasses($item);

        $subnav = $this->_renderChildren($item, $level + 1);
        if (($this->_hasChildren($item) && empty($subnav)) ||
            ((!isset($item['url']) || empty($item['url'])) && !$this->_hasChildren($item))
        ) {
            return '';
        }

        $templater = $this->templater();

        $htmlAttributes = [
            'class' => $cls
        ];

        return $this->formatTemplate('item', [
            'attrs' => $templater->formatAttributes($htmlAttributes),
            'link' => $itemLink,
            'subnav' => $subnav
        ]);
    }

    /**
     * Render the link of the menu item.
     *
     * @param array $item The menu item.
     * @param int $level The current nesting level.
     * @return string
     */
    protected function _renderLink($item, $level)
    {
        $templater = $this->templater();

        if ($item['icon'] ?? false) {
            $attrs = [
                'class' => [
                    $this->config('iconClass'),
                    $item['icon']
                ]
            ];

            $icon = $this->formatTemplate('icon', [
                'attrs' => $templater->formatAttributes($attrs)
            ]);
        } else {
            $icon = null;
        }

        if ($item['name_short'] ?? false) {
            $attrs = [
                'class' => $this->config('nameShortClass')
            ];
            $shortName = $this->formatTemplate('name', [
                'attrs' => $templater->formatAttributes($attrs),
                'shortName' => $item['name_short']
            ]);
        } else {
            $shortName = null;
        }

        if ($item['name'] ?? false) {
            $attrs = [
                'class' => $this->config('nameClass')
            ];
            $name = $this->formatTemplate('name', [
                'attrs' => $templater->formatAttributes($attrs),
                'name' => $item['name']
            ]);
        } else {
            $name = null;
        }

        if ($this->_hasChildren($item)) {
            $attrs = [
                'class' => $this->config('caretClass')
            ];
            $caret = $this->formatTemplate('caret', [
                'attrs' => $templater->formatAttributes($attrs)
            ]);
        } else {
            $caret = null;
        }

        $linkContent = $this->formatTemplate('linkContent', compact('icon', 'shortName', 'name', 'caret'));
        $options = [
            'class' => $this->_buildLinkClasses($level),
            'escape' => false
        ];

        if (isset($item['url']) && !empty($item['url'])) {
            return $this->Guardian->protectedLink($linkContent, $item['url'], $options);
        }

        return $this->Html->link($linkContent, 'javascript:void(0)', $options);
    }

    /**
     * Render the subnavigation of the menu item.
     *
     * @param array $item The menu item.
     * @param int $level The current
     * @return string
     */
    protected function _renderChildren($item, $level)
    {
        if (!$this->_hasChildren($item)) {
            return '';
        }

        $subnavItems = $this->renderNested($item['children'], $level);
        if ($subnavItems === '') {
            return '';
        }

        $templater = $this->templater();

        $htmlAttributes = [
            'class' => $this->_buildSubnavClasses($item, $level)
        ];

        return $this->formatTemplate('subnav', [
            'attrs' => $templater->formatAttributes($htmlAttributes),
            'items' => $subnavItems
        ]);
    }
}
