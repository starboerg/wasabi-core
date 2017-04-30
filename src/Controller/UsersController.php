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
namespace Wasabi\Core\Controller;

use Cake\Core\Configure;
use Cake\Database\Connection;
use Cake\Event\EventDispatcherTrait;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\Query;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use FrankFoerster\Filter\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Entity\Token;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Model\Enum\Permission;
use Wasabi\Core\Model\Table\LoginLogsTable;
use Wasabi\Core\Model\Table\TokensTable;
use Wasabi\Core\Model\Table\UsersTable;
use Wasabi\Core\View\AppView;
use Wasabi\Core\Wasabi;

/**
 * Class UsersController
 *
 * @property UsersTable Users
 * @property TokensTable Tokens
 * @property FilterComponent Filter
 * @property LoginLogsTable LoginLogs
 */
class UsersController extends BackendAppController
{
    use EventDispatcherTrait;
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
        'name' => [
            'modelField' => [
                'Users.lastname',
                'Users.firstname'
            ],
            'type' => 'like',
            'actions' => ['index']
        ],
        'email' => [
            'modelField' => 'Users.email',
            'type' => 'like',
            'actions' => ['index']
        ],
        'group_id' => [
            'type' => 'custom',
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
        'id' => [
            'modelField' => 'Users.id',
            'actions' => ['index']
        ],
        'username' => [
            'modelField' => 'Users.username',
            'default' => 'asc',
            'actions' => ['index']
        ],
        'name' => [
            'modelField' => [
                'Users.lastname',
                'Users.firstname'
            ],
            'custom' => [
                'Users.lastname :dir',
                'Users.firstname :dir'
            ],
            'default' => 'asc',
            'actions' => ['index']
        ],
        'email' => [
            'modelField' => 'Users.email',
            'default' => 'asc',
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
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (!Wasabi::setting('Core.User.has_username')) {
            unset($this->filterFields['username']);
            unset($this->sortFields['username']);
        } else {
            if (isset($this->sortFields['email']['default'])) {
                unset($this->sortFields['email']['default']);
            }
        }

        if (!Wasabi::setting('Core.User.has_firstname_lastname')) {
            unset($this->filterFields['name']);
            unset($this->sortFields['name']);
        } else {
            if (isset($this->sortFields['email']['default'])) {
                unset($this->sortFields['email']['default']);
            }
        }

        $this->filterFields['group_id']['customConditions'] = function ($value) {
            if ((int)$value === 0) {
                $query = $this->Users->findUsersWithNoGroup();
                $userIds = $query->extract('id')->toArray();
            } else {
                $userIds = $this->Users->UsersGroups->find()
                    ->where(['group_id' => $value])
                    ->extract('user_id')
                    ->toArray();
            }
            if (!empty($userIds)) {
                return ['Users.id IN' => $userIds];
            } else {
                return ['Users.id' => 0];
            }
        };

        $this->loadComponent('FrankFoerster/Filter.Filter');
        $this->loadComponent('RequestHandler');
    }

    /**
     * login action
     * GET | POST
     *
     * @return void
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $this->loadModel('Wasabi/Core.LoginLogs');
            $clientIp = $this->request->clientIp();
            $ipIsBlocked = $this->LoginLogs->ipIsBlocked($clientIp);

            if ($ipIsBlocked) {
                $errorMsg = __d('wasabi_core', 'You made too many failed login attempts in a short period of time. Please try again later.');
            }

            if (!$ipIsBlocked && ($user = $this->Auth->identify())) {
                if ((bool)$user['verified'] === false) {
                    $message = __d(
                        'wasabi_core',
                        'Please verify your email address or request a {0}.',
                        '<a href="' . Router::url(['plugin' => 'Wasabi/Core', 'controller' => 'Users', 'action' => 'requestNewVerificationEmail']) . '">' . __d('wasabi_core', 'new verification email') . '</a>'
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
                    $this->request->session()->write('loginTime', time());
                    if (!$this->request->is('ajax')) {
                        $this->Flash->success(__d('wasabi_core', 'Welcome back.'), 'auth');
                        if (($redirectUrl = $this->Auth->redirectUrl()) === '/backend/heartbeat') {
                            $this->redirect(['plugin' => 'Wasabi/Core', 'controller' => 'Dashboard', 'action' => 'index']);
                        } else {
                            $this->redirect($redirectUrl);
                        }
                        return;
                    }
                }
            } else {
                unset($this->request->data['password']);
                $this->request->session()->write('data.login', $this->request->data());
                if (!$ipIsBlocked) {
                    $identityField = Wasabi::setting('Auth.identity_field');
                    $this->dispatchEvent('Auth.failedLogin', [$clientIp, $identityField, $this->request->data[$identityField]]);
                    $errorMsg = __d('wasabi_core', 'Email or password is incorrect.');
                }

                if (isset($errorMsg)) {
                    $this->Flash->error($errorMsg, 'auth', false);
                }
                if (!$this->request->is('ajax')) {
                    $this->redirect(['action' => 'login']);
                    return;
                }
            }

            if ($this->request->is('ajax')) {
                $this->set([
                    'status' => 200,
                    '_serialize' => ['status']
                ]);
                $flashType = $this->request->session()->read('Flash.auth.0.params.type');
                if ($flashType === 'warning') {
                    $this->set('redirect', Router::url('/' . $this->request->url, true));
                    $this->viewVars['_serialize'][] = 'redirect';
                }
                if ($flashType === 'error') {
                    $this->set('content', (new AppView($this->request, $this->response, $this->eventManager()))->element('Wasabi/Core.login-form-ajax'));
                    $this->viewVars['_serialize'][] = 'content';
                }
            }
        } else {
            if ($this->request->session()->check('data.login')) {
                $this->request->data = (array)$this->request->session()->read('data.login');
                $this->request->session()->delete('data.login');
            }
        }

        if (!$this->request->is('ajax')) {
            $this->render(null, 'Wasabi/Core.support');
        }
    }

    /**
     * logout action
     * GET
     *
     * @return void
     */
    public function logout()
    {
        $this->redirect($this->Auth->logout());
        //@codingStandardIgnoreStart
        return;
        //@codingStandardIgnoreEnd
    }

    /**
     * Register action
     * GET | POST
     *
     * @return void
     */
    public function register()
    {
        /** @var User $user */
        $user = $this->Users->newEntity($this->request->data);
        if ($this->request->is('post') && !empty($this->request->data)) {
            if ($this->Users->save($user)) {
                $this->loadModel('Wasabi/Core.Tokens');
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_EMAIL_VERIFICATION);
                $token = $this->Tokens->generateToken($user, TokensTable::TYPE_EMAIL_VERIFICATION);
                $this->getMailer('Wasabi/Core.User')->send('verifyEmail', [$user, $token]);
                $this->Flash->success(__d('wasabi_core', 'Registration successful! We have sent you an email to verify your email address. Please follow the instructions in this email.'));
                $this->redirect(['action' => 'login']);
                return;
            }
            $this->Flash->error($this->formErrorMessage);
        }
        $this->set(['user' => $user]);
        $this->viewBuilder()->layout('Wasabi/Core.support');
    }

    /**
     * Index action
     * GET
     *
     * @return void
     */
    public function index()
    {
        $userQuery = $this->Users->find()
            ->select([
                $this->Users->aliasField('id'),
                $this->Users->aliasField('firstname'),
                $this->Users->aliasField('lastname'),
                $this->Users->aliasField('username'),
                $this->Users->aliasField('email'),
                $this->Users->aliasField('verified'),
                $this->Users->aliasField('active')
            ])
            ->contain([
                'Groups' => [
                    'queryBuilder' => function (Query $q) {
                        return $q
                            ->select([
                                $this->Users->Groups->aliasField('id'),
                                $this->Users->Groups->aliasField('name')
                            ]);
                    }
                ]
            ]);

        $groups = $this->Users->Groups->find('list')->order(['name' => 'ASC'])->toArray();
        $groups[0] = __d('wasabi_core', '--- (unassigned)');

        $this->set([
            'users' => $this->Filter->paginate($this->Filter->filter($userQuery)),
            'groups' => $groups
        ]);
    }

    /**
     * Add action
     * GET | POST
     *
     * @return void
     */
    public function add()
    {
        if (!$this->request->is(['get', 'post'])) {
            throw new MethodNotAllowedException();
        }

        /** @var User $user */
        $user = $this->Users->newEntity(null, ['associated' => 'Groups']);

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data, ['associated' => 'Groups']);
            $user->set('password', Text::uuid());

            if ($this->Users->save($user, ['associated' => 'Groups'])) {
                $this->Users->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_EMAIL_VERIFICATION);
                $token = $this->Users->Tokens->generateToken($user, TokensTable::TYPE_EMAIL_VERIFICATION);
                $this->getMailer('Wasabi/Core.User')->send('verifyAndResetPasswordEmail', [$user, $token]);
                $this->Flash->success(__d('pb', 'The user <strong>{0}</strong> has been created.', $user->fullName()));
                $this->redirect(['action' => 'index']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $groups = $this->Users->Groups->find('list')->order('id ASC');

        // users that are no admin, may not select the super admin group
        if (Wasabi::user()->hasAccessLevel(Permission::OWN)) {
            $groups->where(['id <>' => 1]);
        }

        $this->set([
            'user' => $user,
            'groups' => $groups,
            'languages' => Hash::map(Configure::read('languages.backend'), '{n}', function ($language) {
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
     * @param string $id The user id.
     * @return void
     */
    public function edit($id)
    {
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        /** @var User $user */
        $user = $this->Users->getUserAndGroups($id);

        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->data, ['associated' => 'Groups']);
            $userActivated = ($user->dirty('active') && $user->active);
            if ($userActivated) {
                // do not allow a user to be activated if his email address is not verified
                if (!$user->verified) {
                    $user->active = false;
                    $userActivated = false;
                } else {
                    $user->activate();
                }
            }
            $userDeactivated = ($user->dirty('active') && !$user->active);
            if ($userDeactivated) {
                // do not allow a user to deactivate his own account
                if ($user->id === Wasabi::user()->id) {
                    $user->active = true;
                    $userDeactivated = false;
                } else {
                    $user->deactivate();
                }
            }
            if ($this->Users->save($user, ['associated' => 'Groups'])) {
                if ($user->id === Wasabi::user()->id) {
                    $updateUser = $this->Users->get($user->id);
                    $updateUser->group_id = $this->Users->UsersGroups->getGroupIds($updateUser->id);
                    $this->Auth->setUser($updateUser->toArray());
                    Wasabi::user($updateUser);
                }
                if ($userActivated && $user->verified) {
                    $this->getMailer('Wasabi/Core.User')->send('activatedEmail', [$user]);
                }
                if ($userDeactivated && $user->verified) {
                    $this->getMailer('Wasabi/Core.User')->send('deactivatedEmail', [$user]);
                }
                $this->Flash->success(__d('pb', 'The user <strong>{0}</strong> has been updated.', $user->fullName()));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $groups = $this->Users->Groups->find('list')->order('id ASC');

        // users that are no admin, may not select the admin group
        if (Wasabi::user()->hasAccessLevel(Permission::OWN)) {
            $groups->where(['id <>' => 1]);
        }

        $this->set([
            'user' => $user,
            'groups' => $groups,
            'languages' => Hash::map(Configure::read('languages.backend'), '{n}', function ($language) {
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
     * @param string $id The user id.
     * @return void
     */
    public function delete($id)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        /** @var User $user */
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__d('wasabi_core', 'The user <strong>{0}</strong> has been deleted.', $user->username));
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }

        $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
        //@codingStandardIgnoreStart
        return;
        //@codingStandardIgnoreEnd
    }

    /**
     * Verify action
     * GET | POST | PUT
     *
     * @param string $id The user id.
     * @return void
     */
    public function verify($id)
    {
        if (!$this->request->is(['get', 'post', 'put'])) {
            throw new MethodNotAllowedException();
        }

        /** @var User $user */
        $user = $this->Users->get($id);

        if ($this->request->is(['post', 'put'])) {
            if ($user->verified) {
                $this->Flash->warning(__d('wasabi_core', 'The email address of user <strong>{0}</strong> is already verified.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            }
            if ($this->Users->verify($user, true)) {
                $this->getMailer('Wasabi/Core.User')->send('verifiedByAdminEmail', [$user]);
                $this->Flash->success(__d('wasabi_core', 'The email address of user <strong>{0}</strong> has been verified.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->dbErrorMessage);
            }
        }

        $this->set([
            'user' => $user
        ]);
    }

    /**
     * HeartBeat action
     * AJAX POST
     *
     * @return void
     */
    public function heartBeat()
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        $loginTime = $this->request->session()->check('loginTime') ? $this->request->session()->read('loginTime') : 0;
        $maxLoggedInTime = (int)Wasabi::setting('Core.Login.HeartBeat.max_login_time', 0) / 1000;
        $logoutTime = $loginTime + $maxLoggedInTime;

        if (time() <= $logoutTime) {
            $this->set([
                'status' => 200,
                '_serialize' => ['status']
            ]);
        } else {
            $this->Auth->logout();

            $this->set([
                'status' => 401,
                '_serialize' => ['status']
            ]);
        }
    }

    /**
     * Profile action
     * GET | PUT
     *
     * @return void
     */
    public function profile()
    {
        $user = $this->Users->get($this->Auth->user('id'));

        if ($this->request->is('put') && !empty($this->request->data)) {
            /** @var User $user */
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Auth->setUser(Hash::merge($this->Auth->user(), $user->toArray()));
                $this->Flash->success(__d('wasabi_core', 'Your profile has been updated.'));
                $this->redirect(['action' => 'profile']);
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $this->set([
            'user' => $user,
            'languages' => Hash::map(Configure::read('languages.backend'), '{n}', function ($language) {
                return [
                    'value' => $language->id,
                    'text' => $language->name
                ];
            })
        ]);
    }

    /**
     * Activate action
     * GET | POST | PUT
     *
     * @param string $id The user id.
     * @return void
     */
    public function activate($id)
    {
        if (!$this->request->is(['get', 'post', 'put'])) {
            throw new MethodNotAllowedException();
        }

        /** @var User $user */
        $user = $this->Users->get($id);

        if ($this->request->is(['post', 'put'])) {
            if (!$user->verified) {
                $this->Flash->error(__d('wasabi_core', 'The email address of <strong>{0}</strong> must be verified before activation.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            }
            if ($user->active) {
                $this->Flash->warning(__d('wasabi_core', 'The user account of <strong>{0}</strong> is already active.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            }
            if ($this->Users->activate($user)) {
                $this->getMailer('Wasabi/Core.User')->send('activatedEmail', [$user]);
                $this->Flash->success(__d('wasabi_core', 'The user account of <strong>{0}</strong> has been activated.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->dbErrorMessage);
            }
        }

        $this->set([
            'user' => $user
        ]);
    }

    /**
     * Deactivate action
     * GET | POST | PUT
     *
     * @param string $id The user id.
     * @return void
     */
    public function deactivate($id)
    {
        if (!$this->request->is(['get', 'post', 'put'])) {
            throw new MethodNotAllowedException();
        }

        /** @var User $user */
        $user = $this->Users->get($id);

        if ($this->request->is(['post', 'put'])) {
            if (!$user->active) {
                $this->Flash->warning(__d('wasabi_core', 'The user account of <strong>{0}</strong> is already inactive.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            }
            if ($this->Users->deactivate($user)) {
                $this->getMailer('Wasabi/Core.User')->send('deactivatedEmail', [$user]);
                $this->Flash->success(__d('wasabi_core', 'The user account of <strong>{0}</strong> has been deactivated.', $user->username));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->dbErrorMessage);
            }
        }

        $this->set([
            'user' => $user
        ]);
    }

    /**
     * Unauthorized action
     *
     * This action is called whenever a user tries to access a controller action
     * without the proper access rights.
     *
     * @return void
     */
    public function unauthorized()
    {
    }

    /**
     * requestNewVerificationEmail action
     * GET | POST
     *
     * @return void
     */
    public function requestNewVerificationEmail()
    {
        if ($this->request->is('post') && !empty($this->request->data)) {
            /** @var User $user */
            $user = $this->Users->newEntity($this->request->data, ['validate' => 'emailOnly']);
            if (!$user->errors()) {
                if (($user = $this->Users->existsWithEmail($user->email))) {
                    $this->loadModel('Wasabi/Core.Tokens');

                    $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_EMAIL_VERIFICATION);
                    $token = $this->Tokens->generateToken($user, TokensTable::TYPE_EMAIL_VERIFICATION);
                    $this->getMailer('Wasabi/Core.User')->send('verifyEmail', [$user, $token]);
                }
                $this->Flash->success(__d('wasabi_core', 'We have sent you a verification email with instructions on how to verify your email address.'));
                $this->redirect(['action' => 'login']);
                return;
            } else {
                $this->request->session()->write('data.requestNewVerificationEmail', $this->request->data());
                $this->Flash->error($this->formErrorMessage);
                $this->redirect(['action' => 'requestNewVerificationEmail']);
                return;
            }
        } else {
            if ($this->request->session()->check('data.requestNewVerificationEmail')) {
                $this->request->data = (array)$this->request->session()->read('data.requestNewVerificationEmail');
                $this->request->session()->delete('data.requestNewVerificationEmail');
            }
            $user = $this->Users->newEntity($this->request->data, ['validate' => 'emailOnly']);
        }
        $this->set('user', $user);
        $this->render(null, 'Wasabi/Core.support');
    }

    /**
     * lostPassword action
     * GET | POST
     *
     * @return void
     */
    public function lostPassword()
    {
        if ($this->request->is('post') && !empty($this->request->data)) {
            /** @var User $user */
            $user = $this->Users->newEntity($this->request->data, ['validate' => 'emailOnly']);
            if (!$user->errors()) {
                if (($user = $this->Users->existsWithEmail($user->email))) {
                    $this->loadModel('Wasabi/Core.Tokens');

                    $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_LOST_PASSWORD);
                    $token = $this->Tokens->generateToken($user, TokensTable::TYPE_LOST_PASSWORD);
                    $this->getMailer('Wasabi/Core.User')->send('lostPasswordEmail', [$user, $token]);
                }
                $this->Flash->success(__d('wasabi_core', 'We have sent you an email to reset your password.'));
                $this->redirect(['action' => 'login']);
                return;
            } else {
                $this->request->session()->write('data.lostPassword', $this->request->data());
                $this->Flash->error($this->formErrorMessage);
                $this->redirect(['action' => 'lostPassword']);
                return;
            }
        } else {
            if ($this->request->session()->check('data.lostPassword')) {
                $this->request->data = (array)$this->request->session()->read('data.lostPassword');
                $this->request->session()->delete('data.lostPassword');
            }
            $user = $this->Users->newEntity($this->request->data, ['validate' => 'emailOnly']);
        }
        $this->set('user', $user);
        $this->render(null, 'Wasabi/Core.support');
    }

    /**
     * resetPassword action
     * GET | POST
     *
     * @param string $tokenString The reset password token.
     * @return void
     */
    public function resetPassword($tokenString)
    {
        $this->loadModel('Wasabi/Core.Tokens');
        /** @var Token $token */
        if (!$tokenString || !($token = $this->Tokens->findByToken($tokenString)) ||
            $token->hasExpired() || $token->used
        ) {
            $this->redirect('/');
            return;
        }

        $user = $this->Users->get($token->user_id, [
            'fields' => ['id']
        ]);

        if ($this->request->is('put') && !empty($this->request->data)) {
            /** @var User $user */
            $user = $this->Users->patchEntity($user, $this->request->data, ['validate' => 'passwordOnly']);
            /** @var Connection $connection */
            $connection = $this->Users->connection();
            $connection->begin();
            if ($this->Users->save($user)) {
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_LOST_PASSWORD);
                $connection->commit();

                $this->Flash->success(__d('wasabi_core', 'Your password has been changed successfully.'));
                $this->redirect(['action' => 'login']);
                return;
            } else {
                $connection->rollback();
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set('user', $user);
        $this->render(null, 'Wasabi/Core.support');
    }

    /**
     * verifyByToken action
     * GET
     *
     * @param string $tokenString The verification token.
     * @return void
     */
    public function verifyByToken($tokenString)
    {
        $this->loadModel('Wasabi/Core.Tokens');

        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        /** @var Token $token */
        if ($tokenString && (bool)($token = $this->Tokens->findByToken($tokenString)) &&
            !$token->hasExpired() && !$token->used
        ) {
            /** @var User $user */
            $user = $this->Users->get($token->user_id);

            /** @var Connection $connection */
            $connection = $this->Users->connection();
            $connection->begin();
            if ($this->Users->verify($user)) {
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_EMAIL_VERIFICATION);
                $connection->commit();
            } else {
                $connection->rollback();
            }
        }

        $this->render(null, 'Wasabi/Core.support');
    }
}
