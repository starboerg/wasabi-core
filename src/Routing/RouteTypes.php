<?php

namespace Wasabi\Core\Routing;

class RouteTypes {

    const TYPE_DEFAULT_ROUTE  = 1;
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
     */
    protected static function _init()
    {
        self::$_routeTypes = [
            self::TYPE_DEFAULT_ROUTE => __d('wasabi_core', 'Default Route'),
            self::TYPE_REDIRECT_ROUTE => __d('wasabi_core', 'Redirect Route')
        ];

        self::$_initialized = true;
    }

    /**
     * Get the translated name of a route type with the given $routeTypeId.
     *
     * @param int|string $routeTypeId
     * @return bool|string
     */
    public static function get($routeTypeId)
    {
        if (!self::$_initialized) {
            self::_init();
        }

        $routeTypeId = (int) $routeTypeId;

        if (isset(self::$_routeTypes[$routeTypeId])) {
            return self::$_routeTypes[$routeTypeId];
        }

        return false;
    }
}
