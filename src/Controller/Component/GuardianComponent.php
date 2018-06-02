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
namespace Wasabi\Core\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Exception\Exception;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Filesystem\Folder;
use Cake\Network\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Wasabi\Core\Model\Table\GroupPermissionsTable;
use Wasabi\Core\Permission\PermissionManager;
use Wasabi\Core\Wasabi;

/**
 * Class GuardianComponent
 *
 * @property Component\AuthComponent $Auth
 */
class GuardianComponent extends Component
{
    use EventDispatcherTrait;

    /**
     * Other components used by this component.
     *
     * @var array
     */
    public $components = ['Auth'];

    /**
     * Request object
     *
     * @var \Cake\Network\Request
     */
    public $request;

    /**
     * Response object
     *
     * @var \Cake\Network\Response
     */
    public $response;

    /**
     * Instance of the Session object
     *
     * @var Session
     */
    public $session;

    /**
     * Holds the permission manager instance.
     *
     * @var PermissionManager
     */
    public $permissionManager;

    /**
     * @var GuardianComponent
     */
    protected static $_instance;

    /**
     * Holds an instance of the GroupPermissionsTable.
     *
     * @var GroupPermissionsTable
     */
    protected $GroupPermissions;

    /**
     * Holds all public accessible action paths.
     *
     * Structure:
     * ----------
     * Array(
     *     'Plugin.Controller.action',
     *     'Plugin.Controller.action2,
     *     ...
     * )
     *
     * Global access via:
     * ------------------
     * Guardian::getGuestActions();
     *
     * @var array
     */
    protected $_guestActions = [];

    /**
     * Holds already generated paths indexed by an md5 hash of the url.
     *
     * @var array
     */
    protected $_cachedPaths = [];

    /**
     * Called before the Controller::beforeFilter().
     *
     * @param array $config The configuration settings provided to this component.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->request = $this->getController()->request;
        $this->response = $this->getController()->response;
        $this->session = $this->request->getSession();
        $this->permissionManager = new PermissionManager();

        $this->setEventManager($this->getController()->getEventManager());
        $this->getEventManager()->dispatch(new Event('Guardian.getGuestActions', $this));

        if (!self::$_instance) {
            self::$_instance = &$this;
        }
    }

    /**
     * Get the instantiated GuardianComponent instance.
     *
     * @throws Exception
     * @return GuardianComponent
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new Exception('Please load the GuardianComponent in your Controller.');
        }
        return self::$_instance;
    }

    /**
     * Add guest actions which don't require a logged in user.
     *
     * @param array|string $guestActions The guest action to add.
     * @return void
     */
    public function addGuestActions($guestActions)
    {
        if (!is_array($guestActions)) {
            $guestActions = [$guestActions];
        }

        $this->_guestActions = Hash::merge($this->_guestActions, $guestActions);
    }

    /**
     * Check if the currently logged in user is authorized to access the given url.
     *
     * @param array $url The url parameters.
     * @return bool
     */
    public function hasAccess($url)
    {
        if ($this->isGuestAction($url)) {
            return true;
        }

        $path = $this->getPathFromUrl($url);

        $groupId = $this->Auth->user('group_id');

        if ($groupId === null) {
            return false;
        }

        if (!is_array($groupId)) {
            $groupId = [$groupId];
        }

        if (in_array(1, $groupId)) {
            return true;
        }

        $user = Wasabi::user();
        if (empty($user->permissions)) {
            Wasabi::user()->permissions = $this->_getGroupPermissions()->findAllForGroup($groupId);
        }

        if (array_key_exists($path, Wasabi::user()->permissions)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the requested action does not require an authenticated user.
     * -> guest action
     *
     * @param array $url The url parameters.
     * @return bool
     */
    public function isGuestAction($url)
    {
        $path = $this->getPathFromUrl($url);

        if (in_array($path, $this->_guestActions)) {
            return true;
        }

        return false;
    }

    /**
     * Get all public available action paths.
     *
     * @return array
     */
    public function getGuestActions()
    {
        return $this->_guestActions;
    }

    /**
     * Determine the path for a given url array.
     *
     * @param array $url The url parameters.
     * @return string
     */
    public function getPathFromUrl($url)
    {
        $cacheKey = md5(serialize($url));

        return Cache::remember($cacheKey, function () use ($url) {
            $plugin = 'App';
            $action = 'index';

            $parsedUrl = !is_array($url) ? Router::parse((string)$url) : $url;

            if (isset($parsedUrl['plugin']) && !empty($parsedUrl['plugin'])) {
                $plugin = $parsedUrl['plugin'];
            }
            $controller = $parsedUrl['controller'];
            if (isset($parsedUrl['action']) && $parsedUrl['action'] !== '') {
                $action = $parsedUrl['action'];
            }
            $prefix = '';
            if (isset($parsedUrl['prefix'])) {
                $prefixes = array_map(
                    'Cake\Utility\Inflector::camelize',
                    explode('/', $parsedUrl['prefix'])
                );
                $prefix = implode('/', $prefixes);
                if (!empty($prefix)) {
                    $prefix .= '/';
                }
            }

            return $plugin . '.' . $prefix . $controller . '.' . $action;
        }, 'wasabi/core/guardian_paths');
    }

    /**
     * Load defined permissions from Wasabi/Core, the app and other Plugins listening
     * on the Guardian.Permission.initialize event.
     *
     * @return void
     */
    public function loadPermissions()
    {
        $this->getEventManager()->dispatch(
            new Event('Guardian.Permissions.initialize', $this->permissionManager)
        );
    }

    /**
     * Get a mapped array of all guardable controller actions
     * excluding the provided guest actions.
     *
     * @return array
     */
    public function getActionMap()
    {
        $plugins = $this->getLoadedPluginPaths();

        $actionMap = [];
        foreach ($plugins as $plugin => $path) {
            $controllers = $this->getControllersForPlugin($plugin, $path);
            foreach ($controllers as $controller) {
                $actions = $this->introspectController($controller['path']);
                if (empty($actions)) {
                    continue;
                }
                foreach ($actions as $action) {
                    $path = "{$plugin}.{$controller['name']}.{$action}";
                    if (in_array($path, $this->_guestActions)) {
                        continue;
                    }
                    $actionMap[$path] = [
                        'plugin' => $plugin,
                        'controller' => $controller['name'],
                        'action' => $action
                    ];
                }
            }
        }

        $appControllers = $this->getControllersForApp();
        foreach ($appControllers as $controller) {
            $actions = $this->introspectController($controller['path']);

            if (empty($actions)) {
                continue;
            }
            foreach ($actions as $action) {
                if (strpos($controller['path'], ROOT . DS . 'src' . DS . 'Controller') === false) {
                    $path = "App.{$controller['name']}.{$action}";
                    if (in_array($path, $this->_guestActions)) {
                        continue;
                    }
                    $actionMap[$path] = [
                        'plugin' => 'App',
                        'controller' => $controller['name'],
                        'action' => $action
                    ];
                } else {
                    $namespaceParts = explode(DS, substr($controller['path'], strlen(ROOT . DS . 'src' . DS . 'Controller') + 1));
                    array_pop($namespaceParts);
                    $namespace = join('/', $namespaceParts);
                    if (!empty($namespace)) {
                        $path = "App.{$namespace}/{$controller['name']}.{$action}";
                    } else {
                        $path = "App.{$controller['name']}.{$action}";
                    }
                    if (in_array($path, $this->_guestActions)) {
                        continue;
                    }
                    $actionMap[$path] = [
                        'plugin' => 'App',
                        'controller' => !empty($namespace) ? $namespace . '/' . $controller['name'] : $controller['name'],
                        'action' => $action
                    ];
                }
            }
        }

        return $actionMap;
    }

    /**
     * Get the paths of all installed and active plugins.
     *
     * @return array
     */
    public function getLoadedPluginPaths()
    {
        $pluginPaths = [];

        $plugins = Plugin::loaded() ?? [];
        foreach ($plugins as $p) {
            if (in_array($p, ['DebugKit', 'Migrations'])) {
                continue;
            }
            $pluginPaths[$p] = Plugin::path($p);
        }

        return $pluginPaths;
    }

    /**
     * Retrieve all controller names + paths for a given plugin.
     *
     * @param string $plugin The name of the plugin.
     * @param string $pluginPath The path of the plugin.
     * @return array
     * @todo: Refactor https://scrutinizer-ci.com/g/wasabi-cms/core/indices/834500/duplications/543470
     */
    public function getControllersForPlugin($plugin, $pluginPath)
    {
        $controllers = [];
        $Folder = new Folder();

        $ctrlFolder = $Folder->cd($pluginPath . DS . 'src' . DS . 'Controller');

        if (!empty($ctrlFolder)) {
            $files = $Folder->find('.*Controller\.php$');
            $subLength = strlen('Controller.php');
            foreach ($files as $f) {
                $filename = basename($f);
                if ($filename === $plugin . 'AppController.php') {
                    continue;
                }
                $ctrlName = substr($filename, 0, strlen($filename) - $subLength);
                $controllers[] = [
                    'name' => $ctrlName,
                    'path' => $Folder->path . DS . $f
                ];
            }
        }

        return $controllers;
    }

    /**
     * Retrieve all controller names + paths for the app src.
     *
     * @return array
     */
    public function getControllersForApp()
    {
        $controllers = [];
        $ctrlFolder = new Folder();

        /** @var Folder $ctrlFolder */
        $ctrlFolder->cd(ROOT . DS . 'src' . DS . 'Controller');

        if (!empty($ctrlFolder)) {
            $files = $ctrlFolder->find('.*Controller\.php$');
            $subLength = strlen('Controller.php');
            foreach ($files as $f) {
                $filename = basename($f);
                if ($filename === 'AppController.php') {
                    continue;
                }
                $ctrlName = substr($filename, 0, strlen($filename) - $subLength);
                $controllers[] = [
                    'name' => $ctrlName,
                    'path' => $ctrlFolder->path . DS . $f
                ];
            }
            $subFolders = $ctrlFolder->read(true, false, true)[0];
            foreach ($subFolders as $subFolder) {
                $ctrlFolder->cd($subFolder);
                $files = $ctrlFolder->find('.*Controller\.php$');
                $subLength = strlen('Controller.php');
                foreach ($files as $f) {
                    $filename = basename($f);
                    if ($filename === 'AppController.php') {
                        continue;
                    }
                    $ctrlName = substr($filename, 0, strlen($filename) - $subLength);
                    $controllers[] = [
                        'name' => $ctrlName,
                        'path' => $ctrlFolder->path . DS . $f
                    ];
                }
            }
        }

        return $controllers;
    }

    /**
     * Retrieve all controller actions from a given controller.
     *
     * @param string $controllerPath The path of a specific controller.
     * @return array
     */
    public function introspectController($controllerPath)
    {
        $content = file_get_contents($controllerPath);
        preg_match_all('/public\s+function\s+\&?\s*([^(]+)/', $content, $methods);

        $guardableActions = [];
        foreach ($methods[1] as $m) {
            if (in_array($m, ['__construct', 'initialize', 'isAuthorized', 'setRequest', 'invokeAction', 'beforeFilter', 'beforeRender', 'beforeRedirect', 'afterFilter'])) {
                continue;
            }
            $guardableActions[] = $m;
        }

        return $guardableActions;
    }

    /**
     * Get or initialize an instance of the GroupPermissionsTable.
     *
     * @return GroupPermissionsTable
     */
    protected function _getGroupPermissions()
    {
        if (get_class($this->GroupPermissions) === 'GroupPermission') {
            return $this->GroupPermissions;
        }

        $this->GroupPermissions = TableRegistry::get('Wasabi/Core.GroupPermissions');

        return $this->GroupPermissions;
    }
}
