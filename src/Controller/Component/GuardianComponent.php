<?php
/**
 * Guardian Component
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
namespace Wasabi\Core\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Event\EventManagerTrait;
use Cake\Network\Session;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Wasabi\Core\Model\Table\GroupPermissionsTable;

/**
 * Class GuardianComponent
 *
 * @property Component\AuthComponent $Auth
 */
class GuardianComponent extends Component
{
    use EventManagerTrait;

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
     * @var GuardianComponent
     */
    protected static $_instance;

    /**
     * Holds an instance of the GroupPermission model.
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
     * @param array $config
     */
    public function initialize(array $config)
    {
        $controller = $this->_registry->getController();
        $this->request = $controller->request;
        $this->response = $controller->response;
        $this->session = $controller->request->session();

        $this->eventManager($controller->eventManager());
        $this->eventManager()->dispatch(new Event('Guardian.getGuestActions', $this));

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
     * @param array|string $guestActions
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
     * @param array $url
     * @return bool
     */
    public function hasAccess($url)
    {
        $path = $this->getPathFromUrl($url);

        $groupId = $this->Auth->user('group_id');

        if ($groupId === null) {
            return false;
        }

        if ($groupId === 1) {
            return true;
        }

        $permissions = $this->_getGroupPermissions()->findAllForGroup($groupId);
        if (in_array($path, $permissions)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the requested action does not require an authenticated user.
     * -> guest action
     *
     * @param $url
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
     * @param array $url
     * @return string
     */
    public function getPathFromUrl($url)
    {
        $cacheKey = md5(serialize($url));

        return Cache::remember($cacheKey, function () use ($url) {
            $plugin = 'App';
            $controller = null;
            $action = 'index';

            if (!is_array($url)) {
                $url = Router::parse($url);
            }

            if (isset($url['plugin'])
                && $url['plugin'] !== ''
                && $url['plugin'] !== false
                && $url['plugin'] !== null
            ) {
                $plugin = $url['plugin'];
            }
            $controller = $url['controller'];
            if (isset($url['action']) && $url['action'] !== '') {
                $action = $url['action'];
            }

            return $plugin . '.' . $controller . '.' . $action;
        }, 'wasabi/core/guardian_paths');
    }

    /**
     * Get a mapped array of all guardable controller actions
     * excluding the provided guest actions.
     *
     * @return array
     */
    public function getActionMap()
    {
//		$plugins = $this->getLoadedPluginPaths();
//
//		$actionMap = [];
//		foreach ($plugins as $plugin => $path) {
//			$controllers = $this->getControllersForPlugin($plugin, $path);
//			foreach ($controllers as $controller) {
//				$actions = $this->introspectController($controller['path']);
//				if (empty($actions)) {
//					continue;
//				}
//				foreach ($actions as $action) {
//					$path = "{$plugin}.{$controller['name']}.{$action}";
//					if (in_array($path, $this->_guestActions)) {
//						continue;
//					}
//					$actionMap[$path] = array(
//						'plugin' => $plugin,
//						'controller' => $controller['name'],
//						'action' => $action
//					);
//				}
//			}
//		}
//
//		return $actionMap;
    }

    /**
     * Get the paths of all installed and active plugins.
     *
     * @return array
     */
    public function getLoadedPluginPaths()
    {
//		$pluginPaths = [];
//
//		$plugins = CakePlugin::loaded();
//		foreach ($plugins as $p) {
//			$pluginPaths[$p] = CakePlugin::path($p);
//		}
//
//		return $pluginPaths;
    }

    /**
     * Retrieve all controller names + paths for a given plugin.
     *
     * @param string $plugin
     * @param string $pluginPath
     * @return array
     */
    public function getControllersForPlugin($plugin, $pluginPath)
    {
//		$controllers = [];
//		$Folder = new Folder();
//
//		$ctrlFolder = $Folder->cd($pluginPath . 'Controller');
//
//		if (!empty($ctrlFolder)) {
//			$files = $Folder->find('.*Controller\.php$');
//			$subLength = strlen('Controller.php');
//			foreach ($files as $f) {
//				$filename = basename($f);
//				if ($filename === $plugin . 'AppController.php') {
//					continue;
//				}
//				$ctrlName = substr($filename, 0, strlen($filename) - $subLength);
//				$controllers[] = array(
//					'name' => $ctrlName,
//					'path' => $Folder->path . DS . $f
//				);
//			}
//		}
//
//		return $controllers;
    }

    /**
     * Retrieve all controller actions from a given controller.
     *
     * @param string $controllerPath
     * @return array
     */
    public function introspectController($controllerPath)
    {
//		$content = file_get_contents($controllerPath);
//		preg_match_all('/public\s+function\s+\&?\s*([^(]+)/', $content, $methods);
//
//		$guardableActions = [];
//		foreach ($methods[1] as $m) {
//			if (in_array($m, array('__construct', 'setRequest', 'invokeAction', 'beforeFilter', 'beforeRender', 'beforeRedirect', 'afterFilter'))) {
//				continue;
//			}
//			$guardableActions[] = $m;
//		}
//
//		return $guardableActions;
    }

//	/**
//	 * Get or initialize an instance of the Plugin model.
//	 *
//	 * @return Plugin
//	 */
    protected function _getPluginModel()
    {
//		if (get_class($this->Plugin) === 'Plugin') {
//			return $this->Plugin;
//		}
//		return $this->Plugin = ClassRegistry::init('Core.Plugin');
    }

    /**
     * Get or initialize an instance of the GroupPermission model.
     *
     * @return GroupPermissionsTable
     */
    protected function _getGroupPermissions()
    {
        if (get_class($this->GroupPermissions) === 'GroupPermission') {
            return $this->GroupPermissions;
        }
        return $this->GroupPermissions = TableRegistry::get('Wasabi/Core.GroupPermissions');
    }
}
