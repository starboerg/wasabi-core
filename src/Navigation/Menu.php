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
namespace Wasabi\Core\Navigation;

use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class Menu
{
    /**
     * @var string
     */
    public $alias;

    /**
     * @var array
     */
    protected $_menuItems;

    /**
     * @var array
     */
    protected $_orderedItems;

    /**
     * Constructor
     *
     * @param string $alias The alias of the menu.
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Add a new menu item
     *
     * @param array $options The options of the menu item.
     * @throws Exception
     * @return Menu
     */
    public function addMenuItem($options)
    {
        foreach (['alias', 'name', 'priority'] as $attr) {
            if (!isset($options[$attr]) || (isset($options[$attr]) && $options[$attr] === '')) {
                throw new Exception('$options[\'' . $attr . '\'] is missing.');
            }
        }

        $menuItem = [
            'alias' => $options['alias'],
            'name' => $options['name'],
            'priority' => $options['priority'],
            'matchAction' => $options['matchAction'] ?? false,
            'doNotMatchAction' => [],
            'linkOptions' => $options['linkOptions'] ?? []
        ];

        if (isset($options['name_short'])) {
            $menuItem['name_short'] = $options['name_short'];
        }

        if (isset($options['icon'])) {
            $menuItem['icon'] = $options['icon'];
        }

        if (isset($options['url']) && is_array($options['url']) && !empty($options['url'])) {
            $url = $options['url'];
            if (!isset($url['plugin']) || (isset($url['plugin']) && $url['plugin'] === '')) {
                $url['plugin'] = null;
            }
            if (!isset($url['controller'])) {
                throw new Exception('$options[\'url\'][\'controller\'] is missing.');
            }
            if (!isset($url['action'])) {
                throw new Exception('$options[\'url\'][\'action\'] is missing.');
            }
            $menuItem['url'] = $url;
        }

        if (isset($options['doNotMatchAction'])) {
            $menuItem['doNotMatchAction'] = is_array($options['doNotMatchAction'])
                ? $options['doNotMatchAction']
                : [$options['doNotMatchAction']];
        }

        if (isset($options['parent'])) {
            if (!isset($this->_menuItems[$options['parent']])) {
                throw new Exception(
                    Text::insert('No menu item with the alias specified in $options[\'parent\'] ":parent" exists.', [
                        'parent' => $options['parent']
                    ])
                );
            }
            $menuItem['parent'] = $options['parent'];
            $this->_menuItems[$menuItem['parent']]['children'][$menuItem['alias']] = $menuItem;
        } else {
            $this->_menuItems[$menuItem['alias']] = $menuItem;
        }

        return $this;
    }

    /**
     * Remove a menu item with the given $alias.
     *
     * @param string $alias The alias of the menu item.
     * @param null|string $parent An optional parent menu item.
     * @return void
     */
    public function removeMenuItem($alias, $parent = null)
    {
        if ($parent !== null) {
            if (!isset($this->_menuItems[$parent]) ||
                !isset($this->_menuItems[$parent]['children']) ||
                !isset($this->_menuItems[$parent]['children'][$alias])
            ) {
                return;
            }
            unset($this->_menuItems[$parent]['children'][$alias]);
            return;
        }

        if (!isset($this->_menuItems[$alias])) {
            return;
        }
        unset($this->_menuItems[$alias]);
    }

    /**
     * Create and return an array clone of menu items ordered by their priority.
     *
     * @param array $items The menu items to order.
     * @return array
     */
    public function getOrderedArray($items = [])
    {
        $ordered = [];
        if (empty($items)) {
            $items = $this->_menuItems;
        }

        foreach ($items as $item) {
            $ordered[] = $item;
        }

        $ordered = Hash::sort($ordered, '{n}.priority', 'ASC');

        foreach ($ordered as &$item) {
            if (isset($item['children']) && !empty($item['children'])) {
                $item['children'] = $this->getOrderedArray($item['children']);
            }
        }

        return $ordered;
    }
}
