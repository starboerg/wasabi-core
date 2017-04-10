<?php

namespace Wasabi\Core;

class MenuItem
{
    /**
     * The unique alias of the menu item.
     *
     * @var string
     */
    protected $_alias;

    /**
     * The full name of the menu item.
     *
     * @var string
     */
    protected $_name;

    /**
     * The short name of the menu item.
     *
     * @var string
     */
    protected $_nameShort;

    /**
     * The priority of the menu item determines its render position with a menu.
     *
     * @var int
     */
    protected $_priority = 1000;

    /**
     * The icon css class for the menu icon.
     *
     * @var bool|string
     */
    protected $_icon = false;

    /**
     * The url this menu item should point to. (router url array)
     *
     * @var array
     */
    protected $_url;

    /**
     * The name of the parent menu items alias.
     *
     * @var bool|string
     */
    protected $_parent = false;

    /**
     * Use this field to limit the active state of the menu item to only one specific controller action.
     *
     * @var bool|string
     */
    protected $_matchAction = false;

    /**
     * Tell this menu item to not become active on the given controller actions.
     *
     * @var array
     */
    protected $_doNotMatchAction = [];

    /**
     * Additional options passed through to the menu item link.
     *
     * @var array
     */
    protected $_linkOptions = [];

    /**
     * Get or set the alias.
     *
     * @param null|string $alias
     * @return MenuItem|string
     */
    public function alias($alias = null)
    {
        if ($alias === null) {
            return $this->_alias;
        }
        $this->_alias = $alias;

        return $this;
    }

    /**
     * Get or set the name.
     *
     * @param string $name
     * @return MenuItem|string
     */
    public function name($name = null)
    {
        if ($name === null) {
            return $name;
        }
        $this->_name = $name;

        return $this;
    }

    /**
     * Get or set the short name.
     *
     * @param null|string $name
     * @return MenuItem|string
     */
    public function shortName($name = null)
    {
        if ($name === null) {
            return $this->_nameShort;
        }
        $this->_nameShort = $name;

        return $this;
    }

    /**
     * Get or set the priority.
     *
     * @param null|int $priority
     * @return MenuItem|int
     */
    public function priority($priority = null)
    {
        if ($priority === null) {
            return $this->_priority;
        }
        $this->_priority = $priority;

        return $this;
    }

    /**
     * Get or set the icon class.
     *
     * @param null|string $icon
     * @return MenuItem|string
     */
    public function icon($icon = null)
    {
        if ($icon === null) {
            return $this->_icon;
        }
        $this->_icon = $icon;

        return $this;
    }

    /**
     * Get or set the url.
     *
     * @param null|array $url
     * @return MenuItem|array
     */
    public function url($url = null)
    {
        if ($url === null) {
            return $this->_url;
        }
        $this->_url = $url;

        return $this;
    }

    /**
     * Get or set the parent menu alias.
     *
     * @param bool|null|string $parent
     * @return MenuItem|bool|string
     */
    public function parent($parent = null)
    {
        if ($parent === null) {
            return $this->_parent;
        }
        $this->_parent = $parent;

        return $this;
    }

    /**
     * Get or set match action.
     *
     * @param array|bool|null $matchAction
     * @return MenuItem|array|bool
     */
    public function matchAction($matchAction = null)
    {
        if ($matchAction === null) {
            return $this->_matchAction;
        }
        $this->_matchAction = $matchAction;

        return $this;
    }

    /**
     * Add an action which this menu item should not match for.
     *
     * @param array $action
     * @param bool $reset Whether to reset existing 'doNotMatchAction' entries.
     * @return MenuItem|array
     */
    public function doNotMatchAction(array $action, $reset = false)
    {
        if ($reset) {
            $this->_doNotMatchAction = [];
        }
        if (!empty($action)) {
            $this->_doNotMatchAction[] = $action;
        }

        return $this;
    }
}
