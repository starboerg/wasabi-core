<?php
/**
 * Menu
 *
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
namespace Wasabi\Core;

use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;

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
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Add a new menu item
     *
     * @param array $options
     * @return $this
     * @throws Exception
     */
    public function addMenuItem($options)
    {
        if (!isset($options['alias']) || (isset($options['alias']) && $options['alias'] === '')) {
            throw new Exception('$options[\'alias\'] is missing.');
        }
        if (!isset($options['name']) || (isset($options['name']) && $options['name'] === '')) {
            throw new Exception('$options[\'name\'] is missing.');
        }
        if (!isset($options['priority']) || (isset($options['priority']) && $options['priority'] === '')) {
            throw new Exception('$options[\'priority\'] is missing.');
        }

        $menuItem = [
            'alias' => $options['alias'],
            'name' => $options['name'],
            'priority' => $options['priority'],
            'matchAction' => false,
            'doNotMatchAction' => []
        ];

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

        if (isset($options['matchAction']) && $options['matchAction'] === true) {
            $menuItem['matchAction'] = true;
        }

        if (isset($options['doNotMatchAction'])) {
            if (is_array($options['doNotMatchAction']) && count($options['doNotMatchAction']) > 0) {
                $menuItem['doNotMatchAction'] = $options['doNotMatchAction'];
            } else {
                $menuItem['doNotMatchAction'][] = $options['doNotMatchAction'];
            }
        }

        if (isset($options['parent'])) {
            $parts = preg_split('/\//', $options['parent']);
            if (count($parts) === 1) {
                if (!isset($this->_menuItems[$parts[0]])) {
                    throw new Exception('No menu item with the alias specified in $options[\'parent\'] exists.');
                }
                $menuItem['parent'] = $options['parent'];
                $this->_menuItems[$parts[0]]['children'][$menuItem['alias']] = $menuItem;
            }
            if (count($parts) === 2) {
                if (!isset($this->_menuItems[$parts[0]]['children'][$parts[1]])) {
                    throw new Exception('No menu item with the alias specified in $options[\'parent\'] exists.');
                }
                $menuItem['parent'] = $options['parent'];
                $this->_menuItems[$parts[0]]['children'][$parts[1]]['children'][$menuItem['alias']] = $menuItem;
            }
        } else {
            $this->_menuItems[$menuItem['alias']] = $menuItem;
        }

        return $this;
    }

    /**
     * Create and return an array clone of menu items ordered by their priority.
     *
     * @param array $items
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
