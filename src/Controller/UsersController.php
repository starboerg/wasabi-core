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

use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
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

    /**
     * Initialization hook method.
     */
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
                    $this->Flash->warning($message, 'auth', false);
                } elseif ((bool)$user['active'] === false) {
                    $message = __d(
                        'wasabi_core',
                        'Your account has not yet been activated. Once your account has been checked by an administrator, you will receive a notification email.'
                    );
                    unset($this->request->data['password']);
                    $this->Flash->warning($message, 'auth', false);
                } else {
                    $this->Auth->setUser($user);
                    $this->Flash->success(__d('wasabi_core', 'Welcome back <strong>{0}</strong>.', $user['username']), 'auth');
                    $this->redirect($this->Auth->redirectUrl());
                    return;
                }
            } else {
                unset($this->request->data['password']);
                $this->Flash->error(__d('wasabi_core', 'Username or password is incorrect.'), 'auth', false);
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
                $this->Flash->success(__d('wasabi_core', 'The user has been saved.'));
                $this->redirect(['action' => 'register']);
                return;
            }
            $this->Flash->error(__d('wasabi_core', 'Unable to add the user.'));
        }
        $this->set('user', $user);
    }

    /**
     * index action
     * GET
     */
    public function index()
    {
        $users = $this->Users->find('withGroupName')->hydrate(false);
        $this->set('users', $users);
    }

    /**
     * Add action
     * GET | POST
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        $groups = $this->Users->Groups->find('list');
        if ($this->request->is('post') && !empty($this->request->data)) {
            $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('wasabi_core', 'The user <strong>{0}</strong> has been created.', $this->request->data['username']));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'user' => $user,
            'groups' => $groups
        ]);
    }

    /**
     * Edit action
     * GET | PUT
     *
     * @param string $id
     */
    public function edit($id)
    {
        if (!$id || !$this->Users->exists(['id' => $id])) {
            throw new NotFoundException();
        }
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $user = $this->Users->get($id, [
            'fields' => [
                'id',
                'username',
                'email',
                'group_id'
            ]
        ]);
        $groups = $this->Users->Groups->find('list');
        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('wasabi_core', 'The user <strong>{0}</strong> has been saved.', $this->request->data['username']));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'user' => $user,
            'groups' => $groups
        ]);
        $this->render('add');
    }

    /**
     * Delete action
     * POST
     *
     * @param string $id
     */
    public function delete($id)
    {
        if (!$id || !$this->Users->exists($id)) {
            throw new NotFoundException();
        }
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__d('wasabi_core', 'The user <strong>{0}</strong> has been deleted.', $user->username));
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }

        $this->redirect(['action' => 'index']);
        return;
    }

    /**
     * This action is called whenever a user tries to access a controller action
     * without the proper access rights.
     */
    public function unauthorized()
    {
    }
}
