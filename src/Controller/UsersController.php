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
namespace Wasabi\Core\Controller;

use Cake\Routing\Router;
use Wasabi\Core\Model\Table\UsersTable;

/**
 * Class UsersController
 *
 * @property UsersTable $Users
 */
class UsersController extends BackendAppController
{
    /**
     * Filter fields definitions
     *
     * `actions` describes on which controller
     * action this filter field is available.
     *
     * @var array
     */
    public $filterFields = [
        'search' => [
            'modelField' => [
                'User.username',
                'User.email'
            ],
            'type' => 'like',
            'actions' => ['index']
        ]
    ];

    /**
     * Controller actions where slugged filters are used.
     *
     * @var array
     */
    public $sluggedFilterActions = [
        'index'
    ];

    /**
     * Sortable Fields definition
     *
     * `actions` describes on which controller
     * action this field is sortable.
     *
     * @var array
     */
    public $sortFields = [
        'user' => [
            'modelField' => 'User.firstname',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'email' => [
            'modelField' => 'User.email',
            'actions' => ['index']
        ],
        'group' => [
            'modelField' => 'Group.name',
            'actions' => ['index']
        ],
        'status' => [
            'modelField' => 'User.active',
            'actions' => ['index']
        ]
    ];

    /**
     * Limit options determine the available dropdown
     * options (display items per page) for each action.
     *
     * @var array
     */
    public $limits = [
        'index' => [
            'limits' => [10, 25, 50, 75, 100, 150, 200],
            'default' => 10,
            'fieldName' => 'l'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Wasabi/Core.Filter');
    }

    /**
     * login action
     * GET | POST
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                if ((bool)$user['verified'] === false) {
                    $message = __d(
                        'wasabi_core',
                        'Please verify your email address. You can request a new verification email {0}.',
                        '<a href="' . Router::url(array('plugin' => 'Wasabi/Core', 'controller' => 'Users', 'action' => 'requestNewVerificationEmail')) . '">' . __d('wasabi_core', 'here') .'</a>'
                    );
                    unset($this->request->data['password']);
                    $this->Flash->warning($message, ['key' => 'auth']);
                } elseif ((bool)$user['active'] === false) {
                    $message = __d(
                        'wasabi_core',
                        'Your account has not yet been activated. Once your account has been checked by an administrator, you will receive a notification email.'
                    );
                    unset($this->request->data['password']);
                    $this->Flash->warning($message, ['key' => 'auth']);
                } else {
                    $this->Auth->setUser($user);
                    $this->Flash->success(__d('wasabi_core', 'Welcome back <strong>{0}</strong>.', $user['username']), ['key' => 'auth']);
                    $this->redirect($this->Auth->redirectUrl());
                    return;
                }
            } else {
                unset($this->request->data['password']);
                $this->Flash->error(__d('wasabi', 'Username or password is incorrect.'), ['key' => 'auth']);
            }
        }
        $this->render(null, 'support');
    }

    /**
     * logout action
     * GET
     */
    public function logout()
    {
        $this->redirect($this->Auth->logout());
        return;
    }

    /**
     * register action
     * GET | POST
     */
    public function register()
    {
        $user = $this->Users->newEntity($this->request->data);
        if ($this->request->is('post')) {
            $user->set('group_id', 1);
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('wasabi', 'The user has been saved.'));
                $this->redirect(['action' => 'register']);
                return;
            }
            $this->Flash->error(__d('wasabi', 'Unable to add the user.'));
        }
        $this->set('user', $user);
    }

    /**
     * index action
     * GET
     */
    public function index()
    {
        $users = $this->Users->find('all')->contain('Groups')->hydrate(false);
        $this->set('users', $users);
    }

    public function unauthorized()
    {
    }
}
