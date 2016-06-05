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
namespace Wasabi\Core\Routing;

/**
 * Class RouteTypes
 */
class RouteTypes
{
    const TYPE_DEFAULT_ROUTE = 1;
    const TYPE_REDIRECT_ROUTE = 2;

    /**
     * Determines if the route types singleton has been initialized.
     *
     * @var boolean
     */
    protected static $_initialized = false;

    /**
     * Holds all available route types.
     *
     * @var array
     */
    protected static $_routeTypes = [];

    /**
     * Initialization
     *
     * @return void
     */
    protected static function _init()
    {
        self::$_routeTypes = [
            self::TYPE_DEFAULT_ROUTE => __d('wasabi_core', 'Default'),
            self::TYPE_REDIRECT_ROUTE => __d('wasabi_core', 'Redirect')
        ];

        self::$_initialized = true;
    }

    /**
     * Get the translated name of a route type with the given $routeTypeId.
     *
     * @param int|string $routeTypeId The route type id.
     * @return bool|string
     */
    public static function get($routeTypeId)
    {
        if (!self::$_initialized) {
            self::_init();
        }

        $routeTypeId = (int)$routeTypeId;

        if (isset(self::$_routeTypes[$routeTypeId])) {
            return self::$_routeTypes[$routeTypeId];
        }

        return false;
    }

    /**
     * Get all route types for a select.
     *
     * @return array
     */
    public static function getForSelect()
    {
        if (!self::$_initialized) {
            self::_init();
        }

        return self::$_routeTypes;
    }
}
