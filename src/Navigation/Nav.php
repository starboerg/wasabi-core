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
use Cake\Utility\Text;

class Nav
{
    /**
     * Holds all menus.
     *
     * @var Menu[]
     */
    protected static $_menus = [];

    /**
     * Create a new menu.
     *
     * @param string $alias the alias of the menu
     * @throws Exception
     * @return Menu
     */
    public static function createMenu($alias)
    {
        if (isset(self::$_menus[$alias])) {
            throw new Exception(Text::insert('A Menu with alias ":alias" already exists.', ['alias' => $alias]));
        }
        self::$_menus[$alias] = new Menu($alias);

        return self::$_menus[$alias];
    }

    /**
     * Get a Menu instance or an ordered array
     * of menu items of a menu.
     *
     * @param string $alias the alias of the menu
     * @param bool $ordered true: return array of priority ordered menu items, false: return WasabiMenu instance
     * @throws Exception
     * @return array|Menu
     */
    public static function getMenu($alias, $ordered = false)
    {
        if (!isset(self::$_menus[$alias])) {
            throw new Exception(Text::insert('No menu with alias ":alias" does exist.', ['alias' => $alias]));
        }
        if (!$ordered) {
            return self::$_menus[$alias];
        }

        return self::$_menus[$alias]->getOrderedArray();
    }

    /**
     * Check if a menu with the given alias already exists.
     *
     * @param string $alias The alias to check.
     * @return bool
     */
    public static function menuExists($alias)
    {
        return isset(self::$_menus[$alias]);
    }

    /**
     * Clear all menus.
     *
     * @return void
     */
    public static function clear()
    {
        self::$_menus = [];
    }
}
