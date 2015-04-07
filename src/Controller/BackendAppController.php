<?php
/**
 * Wasabi Core Backend App Controller
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
namespace Wasabi\Core\Controller;

use Cake\Controller\Component\AuthComponent;
use Cake\Event\Event;
use Wasabi\Core\Nav;

/**
 * Class BackendAppController
 *
 * @property \Wasabi\Core\Controller\Component\GuardianComponent $Guardian
 */
class BackendAppController extends AppController
{
    /**
     * Helpers available for all backend controllers.
     *
     * @var array
     */
    public $helpers = [
        'Html' => [
            'className' => 'Wasabi/Core.Html'
        ],
        'Menu' => [
            'className' => 'Wasabi/Core.Menu'
        ],
        'Guardian' => [
            'className' => 'Wasabi/Core.Guardian'
        ]
    ];

    /**
     * initialization hook method
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Auth', [
            AuthComponent::ALL => [
                'userModel' => 'Wasabi/Core.Users'
            ],
            'loginAction' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            'unauthorizedRedirect' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'unauthorized'
            ],
            'authorize' => 'Controller'
        ]);

        $this->loadComponent('Wasabi/Core.Guardian');

        // Load all menu items from all plugins.
        $this->eventManager()->dispatch(new Event('Wasabi.Backend.Menu.initMain', Nav::createMenu('backend.main')));

        $this->viewClass = 'Wasabi/Core.App';
    }

    /**
     * beforeFilter callback
     *
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->deny();

        if (!$this->Auth->user()) {
            $this->Auth->config('authError', false);
        }

        $this->_allow();
    }

    /**
     * Check if the current request needs an authenticated user.
     * Check if the user is authorized to complete the request.
     *
     * @param array $user
     * @return bool
     */
    public function isAuthorized($user = null)
    {
        $url = [
            'plugin' => $this->request->params['plugin'],
            'controller' => $this->request->params['controller'],
            'action' => $this->request->params['action']
        ];
        return $this->Guardian->hasAccess($url);
    }

    /**
     * Allow all guest actions.
     */
    protected function _allow()
    {
        $url = [
            'plugin' => $this->request->params['plugin'],
            'controller' => $this->request->params['controller'],
            'action' => $this->request->params['action']
        ];
        if ($this->Guardian->isGuestAction($url)) {
            $this->Auth->allow($this->request->params['action']);
        }
    }
}
