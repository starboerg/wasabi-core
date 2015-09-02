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

use Cake\Core\Configure;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Wasabi\Core\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Table\UsersTable;

/**
 * Class UsersController
 *
 * @property UsersTable $Users
 * @property FilterComponent Filter
 */
class UsersController extends BackendAppController
{
    use MailerAwareTrait;

    /**
     * Filter fields definitions
     *
     * `actions` describes on which controller
     * action this filter field is available.
     *
     * @var array
     */
    public $filterFields = [
        'user_id' => [
            'modelField' => 'Users.id',
            'type' => '=',
            'actions' => ['index']
        ],
        'username' => [
            'modelField' => 'Users.username',
            'type' => 'like',
            'actions' => ['index']
        ],
        'email' => [
            'modelField' => 'Users.email',
            'type' => 'like',
            'actions' => ['index']
        ],
        'group_id' => [
            'modelField' => 'Groups.id',
            'type' => '=',
            'actions' => ['index']
        ]
    ];

    /**
     * Controller actions where slugged filters are used.
     *
     * @var array
     */
    public $filterActions = [
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
            'modelField' => 'Users.username',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'email' => [
            'modelField' => 'Users.email',
            'actions' => ['index']
        ],
        'group' => [
            'modelField' => 'Groups.name',
            'actions' => ['index']
        ],
        'status' => [
            'modelField' => 'Users.active',
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
            'default' => 25,
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
        $this->loadComponent('RequestHandler');
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
                        '<a href="' . Router::url(['plugin' => 'Wasabi/Core', 'controller' => 'Users', 'action' => 'requestNewVerificationEmail']) . '">' . __d('wasabi_core', 'here') .'</a>'
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
        $users = $this->Filter->filter($this->Users->find('withGroupName'));
        $this->set([
            'users' => $this->Filter->paginate($users)->hydrate(false),
            'groups' => $this->Users->Groups->find('list')
        ]);
    }

    /**
     * Add action
     * GET | POST
     */
    public function add()
    {
        $user = $this->Users->newEntity();
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
            'groups' => $this->Users->Groups->find('list'),
            'languages' => Hash::map(Configure::read('languages.backend'), '{n}', function($language) {
                return [
                    'value' => $language->id,
                    'text' => $language->name
                ];
            })
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
                'group_id',
                'language_id',
                'timezone'
            ]
        ]);
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
            'groups' => $this->Users->Groups->find('list'),
            'languages' => Hash::map(Configure::read('languages.backend'), '{n}', function($language) {
                return [
                    'value' => $language->id,
                    'text' => $language->name
                ];
            })
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
        if (!$id || !$this->Users->exists(['id' => $id])) {
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
     * Verify action
     * AJAX POST | GET
     *
     * @param string $id
     */
    public function verify($id)
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        if (!$id || !$this->Users->exists(['id' => $id])) {
            throw new NotFoundException();
        }

        $user = $this->Users->get($id);
        if ($this->Users->verify($user)) {
            $this->getMailer('Wasabi/Core.User')->send('verifiedByAdminEmail', [$user]);
            $this->set([
                'status' => 'success',
                'user' => $user,
                '_serialize' => ['status', 'user']
            ]);
        } else {
            $this->set([
                'error' => $this->dbErrorMessage,
                '_serialize' => ['error']
            ]);
        }
    }

    /**
     * VerifyByToken action
     * GET
     *
     * @param $token
     * @todo /verify/t/345235245389517581
     */
    public function verifyByToken($token)
    {
        if (!$this->Tokens->findByToken($token)) {
            throw new NotFoundException();
        }
        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException();
        }
    }

    /**
     * HeartBeat action
     * AJAX POST
     */
    public function heartBeat()
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }
        $this->request->session()->renew();
        $this->set([
            'status' => 200,
            '_serialize' => ['status']
        ]);
    }

    /**
     * Profile action
     * GET | PUT
     */
    public function profile()
    {
        $user = $this->Users->get($this->Auth->user('id'));

        if ($this->request->is('put') && !empty($this->request->data)) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('wasabi_core', 'Your profile has been updated.'));
                $this->redirect(['action' => 'profile']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $this->set([
            'user' => $user,
            'languages' => Hash::map(Configure::read('languages.backend'), '{n}', function($language) {
                return [
                    'value' => $language->id,
                    'text' => $language->name
                ];
            })
        ]);
    }

    public function activate($id)
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        if (!$id || !$this->Users->exists(['id' => $id])) {
            throw new NotFoundException();
        }

        $user = $this->Users->get($id);
        if ($this->Users->activate($user)) {
            $this->getMailer('Wasabi/Core.User')->send('activationEmail', [$user]);
            $this->set([
                'status' => 'success',
                'user' => $user,
                '_serialize' => ['status', 'user']
            ]);
        } else {
            $this->set([
                'error' => $this->dbErrorMessage,
                '_serialize' => ['error']
            ]);
        }
    }

    /**
     * This action is called whenever a user tries to access a controller action
     * without the proper access rights.
     */
    public function unauthorized()
    {
    }
}
