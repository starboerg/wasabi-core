<?php

namespace Wasabi\Core\View\Helper;

use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\View\Helper;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Model\Table\FiltersTable;

class RouteHelper extends Helper
{
    /**
     * Register route.
     *
     * @return array
     */
    public static function register()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'register',
            'prefix' => null
        ];
    }

    /**
     * Login route.
     *
     * @return array
     */
    public static function login()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Authentication',
            'action' => 'login',
            'prefix' => null
        ];
    }

    /**
     * Logout route.
     *
     * @return array
     */
    public static function logout()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Authentication',
            'action' => 'logout',
            'prefix' => null
        ];
    }

    /**
     * Lost password route.
     *
     * @return array
     */
    public static function lostPassword()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'lostPassword',
            'prefix' => null
        ];
    }

    public static function requestNewVerificationEmail()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'requestNewVerificationEmail',
            'prefix' => null
        ];
    }

    /**
     * Profile route.
     *
     * @return array
     */
    public static function profile()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'profile',
            'prefix' => null
        ];
    }

    /**
     * Dashboard index route.
     *
     * @return array
     */
    public static function dashboardIndex()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Dashboard',
            'action' => 'index',
            'prefix' => null
        ];
    }

    /**
     * Users index route.
     *
     * @return array
     */
    public static function usersIndex()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'index',
            'prefix' => null
        ];
    }

    /**
     * Users index route filtered by active users.
     *
     * @return array
     */
    public static function usersIndexActive()
    {
        return self::filterUrl(self::usersIndex(), [
            'status' => User::STATUS_ACTIVE
        ]);
    }

    /**
     * Users index route filtered by inactive users.
     *
     * @return array
     */
    public static function usersIndexInactive()
    {
        return self::filterUrl(self::usersIndex(), [
            'status' => User::STATUS_INACTIVE
        ]);
    }

    /**
     * Users index route filtered by verified users.
     *
     * @return array
     */
    public static function usersIndexVerified()
    {
        return self::filterUrl(self::usersIndex(), [
            'status' => User::STATUS_VERIFIED
        ]);
    }

    /**
     * Users index route filtered by not verified users.
     *
     * @return array
     */
    public static function usersIndexNotVerified()
    {
        return self::filterUrl(self::usersIndex(), [
            'status' => User::STATUS_NOTVERIFIED
        ]);
    }

    /**
     * Add user route.
     *
     * @return array
     */
    public static function usersAdd()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'add',
            'prefix' => null
        ];
    }

    /**
     * Edit user route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function usersEdit($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'edit',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Delete user route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function usersDelete($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'delete',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Verify user route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function usersVerify($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'verify',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Activate user route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function usersActivate($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'activate',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Deactivate user route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function usersDeactivate($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'deactivate',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Groups index route.
     *
     * @return array
     */
    public static function groupsIndex()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'index',
            'prefix' => null
        ];
    }

    /**
     * Add group route.
     *
     * @return array
     */
    public static function groupsAdd()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'add',
            'prefix' => null
        ];
    }

    /**
     * Edit group route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function groupsEdit($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'edit',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Delete group route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function groupsDelete($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'delete',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Languages index route.
     *
     * @return array
     */
    public static function languagesIndex()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Languages',
            'action' => 'index',
            'prefix' => null
        ];
    }

    /**
     * Add language route.
     *
     * @return array
     */
    public static function languagesAdd()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Languages',
            'action' => 'add',
            'prefix' => null
        ];
    }

    /**
     * Edit language route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function languagesEdit($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Languages',
            'action' => 'edit',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Delete language route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function languagesDelete($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Languages',
            'action' => 'delete',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Change language route.
     *
     * @param integer|string $id
     * @return array
     */
    public static function languagesChange($id)
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Languages',
            'action' => 'change',
            'id' => $id,
            'prefix' => null
        ];
    }

    /**
     * Permissions index route.
     *
     * @return array
     */
    public static function permissionsIndex()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Permissions',
            'action' => 'index',
            'prefix' => null
        ];
    }

    /**
     * Update permissions route.
     *
     * @return array
     */
    public static function permissionsUpdate()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Permissions',
            'action' => 'update',
            'prefix' => null
        ];
    }

    /**
     * Cache settings route.
     *
     * @return array
     */
    public static function settingsCache()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Settings',
            'action' => 'cache',
            'prefix' => null
        ];
    }

    /**
     * General settings route.
     *
     * @return array
     */
    public static function settingsGeneral()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Settings',
            'action' => 'general',
            'prefix' => null
        ];
    }

    /**
     * API login route.
     *
     * @return array
     */
    public static function apiLogin()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Authentication',
            'action' => 'login',
            'prefix' => 'api'
        ];
    }

    /**
     * API heartbeat route.
     *
     * @return array
     */
    public static function apiHeartbeat()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Authentication',
            'action' => 'heartbeat',
            'prefix' => 'api'
        ];
    }

    /**
     * API sort languages route.
     *
     * @return array
     */
    public static function apiLanguagesSort()
    {
        return [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Languages',
            'action' => 'sort',
            'prefix' => 'api'
        ];
    }

    /**
     * Create or get an existing filter url for the given $url array and the provided $filterData.
     *
     * @param array $url
     * @param array $filterData
     * @return array
     */
    protected static function filterUrl(array $url, array $filterData)
    {
        // Find or create a filter slug for each category to link to Ideas::index() and only
        // display a single category/topic.
        $fakeRequest = clone Router::getRequest();
        foreach ($url as $param => $value) {
            $fakeRequest = $fakeRequest->withParam($param, $value);
        }

        /** @var FiltersTable $FiltersTable */
        $FiltersTable = TableRegistry::getTableLocator()->get('Wasabi/Core.Filters');

        $slug = $FiltersTable->findOrCreateSlugForFilterData($fakeRequest, $filterData);

        $url['filterSlug'] = $slug;

        return $url;
    }
}
