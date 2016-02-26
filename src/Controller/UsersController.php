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
use FrankFoerster\Filter\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Entity\Token;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Model\Table\TokensTable;
use Wasabi\Core\Model\Table\UsersTable;
use Wasabi\Core\View\AppView;
use Wasabi\Core\Wasabi;

/**
 * Class UsersController
 *
 * @property UsersTable $Users
 * @property TokensTable $Tokens
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
        $this->loadComponent('FrankFoerster/Filter.Filter');
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
                        '<a href="' . Router::url(['plugin' => 'Wasabi/Core', 'controller' => 'Users', 'action' => 'requestNewVerificationEmail']) . '">' . __d('wasabi_core', 'here') . '</a>'
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
                    if (!$this->request->is('ajax')) {
                        $this->Flash->success(__d('wasabi_core', 'Welcome back.'), 'auth');
                        $this->redirect($this->Auth->redirectUrl());
                        return;
                    }
                }
            } else {
                unset($this->request->data['password']);
                $this->request->session()->write('data.login', $this->request->data());
                if (Configure::read('authenticate.username') === 'email') {
                    $errorMsg = __d('wasabi_core', 'Email or password is incorrect.');
                } else {
                    $errorMsg = __d('wasabi_core', 'Username or password is incorrect.');
                }
                $this->Flash->error($errorMsg, 'auth', false);
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
                $this->request->data = $this->request->session()->read('data.login');
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
     * index action
     * GET
     */
    public function index()
    {
        $users = $this->Filter->filter($this->Users->find('withGroupName'));
        $this->set([
            'users' => $this->Filter->paginate($users),
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
     * @param string $id
     */
    public function edit($id)
    {
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
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }
        $this->set([
            'user' => $user,
            'groups' => $this->Users->Groups->find('list'),
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
     * @param string $id
     */
    public function delete($id)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__d('wasabi_core', 'The user <strong>{0}</strong> has been deleted.', $user->username));
        } else {
            $this->Flash->error($this->dbErrorMessage);
        }

        $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
        return;
    }

    /**
     * Verify action
     * GET | POST | PUT
     *
     * @param string $id
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
     */
    public function heartBeat()
    {
        if (!$this->request->isAll(['ajax', 'post'])) {
            throw new MethodNotAllowedException();
        }

        $heartBeatCount = $this->request->session()->check('heartBeatCount') ? $this->request->session()->read('heartBeatCount') : 0;

        $frequency = $this->_calculateHeartBeatFrequency();
        $maxLoginTime = (int)Wasabi::setting('Core.Login.HeartBeat.max_login_time', 0);
        $maxHeartBeats = $maxLoginTime / $frequency;

        if (++$heartBeatCount < $maxHeartBeats) {
            $this->request->session()->renew();
            $this->request->session()->write('heartBeatCount', $heartBeatCount);

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
     */
    public function profile()
    {
        /** @var User $user */
        $user = $this->Users->get($this->Auth->user('id'));

        if ($this->request->is('put') && !empty($this->request->data)) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->request->session()->write('Auth.User.language_id', $user->language_id);
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
     * activate action
     * GET | POST | PUT
     *
     * @param string $id
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
     * deactivate action
     * GET | POST | PUT
     *
     * @param string $id
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
     * This action is called whenever a user tries to access a controller action
     * without the proper access rights.
     */
    public function unauthorized()
    {
    }

    /**
     * requestNewVerificationEmail action
     * GET | POST
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
                $this->request->data = $this->request->session()->read('data.requestNewVerificationEmail');
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
                $this->request->data = $this->request->session()->read('data.lostPassword');
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
     * @param string $tokenString
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

        /** @var User $user */
        $user = $this->Users->get($token->user_id, [
            'fields' => ['id']
        ]);

        if ($this->request->is('put') && !empty($this->request->data)) {
            $user = $this->Users->patchEntity($user, $this->request->data, ['validate' => 'passwordOnly']);
            $this->Users->connection()->begin();
            if ($this->Users->save($user)) {
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_LOST_PASSWORD);
                $this->Users->connection()->commit();

                $this->Flash->success(__d('wasabi_core', 'Your password has been changed successfully.'));
                $this->redirect(['action' => 'login']);
                return;
            } else {
                $this->Users->connection()->rollback();
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
     * @param string $tokenString
     */
    public function verifyByToken($tokenString)
    {
        $this->loadModel('Wasabi/Core.Tokens');

        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        /** @var Token $token */
        if ($tokenString && !!($token = $this->Tokens->findByToken($tokenString)) &&
            !$token->hasExpired() && !$token->used
        ) {
            /** @var User $user */
            $user = $this->Users->get($token->user_id);

            $this->Users->connection()->begin();
            if ($this->Users->verify($user)) {
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_EMAIL_VERIFICATION);
                $this->Users->connection()->commit();
            } else {
                $this->Users->connection()->rollback();
            }
        }

        $this->render(null, 'Wasabi/Core.support');
    }
}
