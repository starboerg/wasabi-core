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
use Cake\Event\EventDispatcherTrait;
use Cake\Mailer\MailerAwareTrait;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Wasabi\Core\Controller\Component\FilterComponent;
use Wasabi\Core\Model\Entity\Token;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Model\Table\TokensTable;
use Wasabi\Core\Model\Table\UsersTable;
use Wasabi\Core\Permission\Permission;
use Wasabi\Core\Wasabi;

/**
 * Class UsersController
 *
 * @property UsersTable Users
 * @property TokensTable Tokens
 * @property FilterComponent Filter
 */
class UsersController extends BackendAppController
{
    use EventDispatcherTrait;
    use MailerAwareTrait;

    /**
     * Initialization hook method.
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $filterFields = [
            'email',
            'user_id',
            'group_id',
            'status'
        ];

        $sortFields = [
            'id',
            'email',
            'status'
        ];

        $sortDefault = 'email';

        if (Wasabi::setting('Core.User.has_username')) {
            $filterFields[] = 'username';
            $sortFields[] = 'username';
            $sortDefault = 'username';
        }

        if (Wasabi::setting('Core.User.has_firstname_lastname')) {
            $filterFields[] = 'name';
            $sortFields[] = 'name';
        }

        $this->loadComponent('Wasabi/Core.Filter', [
            'index' => [
                'filterFields' => $filterFields,
                'sort' => [
                    'fields' => $sortFields,
                    'default' => $sortDefault,
                    'param' => 's'
                ],
                'limit' => [
                    'available' => [10, 25, 50, 75, 100, 150, 200],
                    'default' => 25,
                    'param' => 'l'
                ],
                'pagination' => [
                    'param' => 'p'
                ]
            ]
        ]);
    }

    /**
     * Register action
     * GET | POST
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function register()
    {
        if (!Configure::read('Core.User.can_register', true)) {
            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->newEntity($this->request->getData());
        if ($this->request->is('post')) {
            if ($this->Users->save($user)) {
                $this->loadModel('Wasabi/Core.Tokens');
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_EMAIL_VERIFICATION);
                $token = $this->Tokens->generateToken($user, TokensTable::TYPE_EMAIL_VERIFICATION);
                $this->getMailer('Wasabi/Core.User')->send('verifyEmail', [$user, $token]);
                $this->Flash->success(__d('wasabi_core', 'Registration successful! We have sent you an email to verify your email address. Please follow the instructions in this email.'));
                $this->redirect(['action' => 'login']);
                return;
            }
            $this->Flash->error($this->formErrorMessage, 'flash', false);
        }
        $this->set(['user' => $user]);
        $this->viewBuilder()->setLayout('Wasabi/Core.support');
    }

    /**
     * Index action
     * GET
     *
     * @param string $filterSlug
     * @return void
     * @throws \Aura\Intl\Exception
     * @throws \Wasabi\Core\Filter\Exception\FilterableTraitNotAppliedException
     */
    public function index($filterSlug = '')
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

        $statusOptions = [
            'verified' => __d('wasabi_core', 'verified'),
            'notverified' => __d('wasabi_core', 'not verified'),
            'active' => __d('wasabi_core', 'active'),
            'inactive' => __d('wasabi_core', 'inactive'),
        ];

        $this->set([
            'users' => $this->Filter->filter($userQuery, $filterSlug),
            'groups' => $groups,
            'statusOptions' => $statusOptions,
            'displayUsername' => Wasabi::setting('Core.User.has_username', false),
            'displayFirstnameLastname' => Wasabi::setting('Core.User.has_firstname_lastname', false),
        ]);
    }

    /**
     * Add action
     * GET | POST
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function add()
    {
        if (!$this->request->is(['get', 'post'])) {
            throw new MethodNotAllowedException();
        }

        $user = $this->Users->newEntity(null, ['associated' => 'Groups']);

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['associated' => 'Groups']);
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
        if (Wasabi::user()->hasAccessLevel(Permission::YES)) {
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
     * @throws \Aura\Intl\Exception
     */
    public function edit($id)
    {
        if (!$this->request->is(['get', 'put'])) {
            throw new MethodNotAllowedException();
        }

        $user = $this->Users->getUserAndGroups($id);

        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['associated' => 'Groups']);
            $userActivated = ($user->isDirty('active') && $user->active);
            if ($userActivated) {
                // do not allow a user to be activated if his email address is not verified
                if (!$user->verified) {
                    $user->active = false;
                    $userActivated = false;
                } else {
                    $user->activate();
                }
            }
            $userDeactivated = ($user->isDirty('active') && !$user->active);
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
                    $updateUser->set('group_id', $this->Users->UsersGroups->getGroupIds($updateUser->id));
                    $this->Auth->setUser($updateUser->toArray());
                    Wasabi::user($updateUser);
                }
                if ($userActivated && $user->verified) {
                    $this->getMailer('Wasabi/Core.User')->send('activatedEmail', [$user]);
                }
                if ($userDeactivated && $user->verified) {
                    $this->getMailer('Wasabi/Core.User')->send('deactivatedEmail', [$user]);
                }
                $this->Flash->success(__d('wasabi_core', 'The user <strong>{0}</strong> has been updated.', $user->fullName()));
                $this->redirect($this->Filter->getBacklink(['action' => 'index'], $this->request));
                return;
            } else {
                $this->Flash->error($this->formErrorMessage);
            }
        }

        $groups = $this->Users->Groups->find('list')->order('id ASC');

        // users that are no admin, may not select the admin group
        if (Wasabi::user()->hasAccessLevel(Permission::YES)) {
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
     * @throws \Aura\Intl\Exception
     */
    public function delete($id)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $user = $this->Users->getUserAndGroups($id);

        if (Wasabi::user()->cant('delete', $user)) {
            $this->redirect($this->Auth->getConfig('unauthorizedRedirect'));
            return;
        }

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
     * @throws \Aura\Intl\Exception
     */
    public function verify($id)
    {
        if (!$this->request->is(['get', 'post', 'put'])) {
            throw new MethodNotAllowedException();
        }

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
     * Profile action
     * GET | PUT
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function profile()
    {
        $user = $this->Users->get($this->Auth->user('id'));

        if ($this->request->is('put')) {
            /** @var User $user */
            $user = $this->Users->patchEntity($user, $this->request->getData());
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
     * @throws \Aura\Intl\Exception
     */
    public function activate($id)
    {
        if (!$this->request->is(['get', 'post', 'put'])) {
            throw new MethodNotAllowedException();
        }

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
     * @throws \Aura\Intl\Exception
     */
    public function deactivate($id)
    {
        if (!$this->request->is(['get', 'post', 'put'])) {
            throw new MethodNotAllowedException();
        }

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
     * @throws \Aura\Intl\Exception
     */
    public function requestNewVerificationEmail()
    {
        if ($this->request->is('post')) {
            $user = $this->Users->newEntity($this->request->getData(), ['validate' => 'emailOnly']);
            if (!$user->getErrors()) {
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
                $this->request->getSession()->write('data.requestNewVerificationEmail', $this->request->getData());
                $this->Flash->error($this->formErrorMessage);
                $this->redirect(['action' => 'requestNewVerificationEmail']);
                return;
            }
        } else {
            if ($this->request->getSession()->check('data.requestNewVerificationEmail')) {
                $this->request->data = (array)$this->request->getSession()->read('data.requestNewVerificationEmail');
                $this->request->getSession()->delete('data.requestNewVerificationEmail');
            }
            $user = $this->Users->newEntity($this->request->getData(), ['validate' => 'emailOnly']);
        }
        $this->set('user', $user);
        $this->render(null, 'Wasabi/Core.support');
    }

    /**
     * lostPassword action
     * GET | POST
     *
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function lostPassword()
    {
        if ($this->request->is('post')) {
            /** @var User $user */
            $user = $this->Users->newEntity($this->request->getData(), ['validate' => 'emailOnly']);
            if (!$user->getErrors()) {
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
                $this->request->getSession()->write('data.lostPassword', $this->request->getData());
                $this->Flash->error($this->formErrorMessage, 'flash', false);
                $this->redirect(['action' => 'lostPassword']);
                return;
            }
        } else {
            if ($this->request->getSession()->check('data.lostPassword')) {
                $this->request->data = (array)$this->request->getSession()->read('data.lostPassword');
                $this->request->getSession()->delete('data.lostPassword');
            }
            $user = $this->Users->newEntity($this->request->getData(), ['validate' => 'emailOnly']);
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
     * @throws \Aura\Intl\Exception
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

        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'passwordOnly']);
            $connection = $this->Users->getConnection();
            $connection->begin();
            if ($this->Users->save($user)) {
                $this->Tokens->invalidateExistingTokens($user->id, TokensTable::TYPE_LOST_PASSWORD);
                $connection->commit();

                $this->Flash->success(__d('wasabi_core', 'Your password has been changed successfully.'));
                $this->redirect(['action' => 'login']);
                return;
            } else {
                $connection->rollback();
                $this->Flash->error($this->formErrorMessage, 'flash', false);
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
            $user = $this->Users->get($token->user_id);

            $connection = $this->Users->getConnection();
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
