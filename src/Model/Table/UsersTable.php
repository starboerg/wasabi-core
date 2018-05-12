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
namespace Wasabi\Core\Model\Table;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use DateTimeZone;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Wasabi;

/**
 * Class UsersTable
 *
 * @property GroupsTable Groups
 * @property UsersGroupsTable UsersGroups
 * @property TokensTable Tokens
 */
class UsersTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->belongsToMany('Groups', [
            'className' => 'Wasabi/Core.Groups',
            'through' => 'Wasabi/Core.UsersGroups'
        ]);
        $this->hasMany('UsersGroups', [
            'className' => 'Wasabi/Core.UsersGroups'
        ]);
        $this->hasMany('Tokens', [
            'className' => 'Wasabi/Core.Tokens'
        ]);

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmpty('email', __d('wasabi_core', 'Please enter an email address.'))
            ->add('email', [
                'email' => [
                    'rule' => 'email',
                    'message' => __d('wasabi_core', 'Please enter a valid email address.')
                ]
            ])
            ->notEmpty('password', __d('wasabi_core', 'Please enter a password.'), 'create')
            ->add('password', [
                'length' => [
                    'rule' => ['minLength', 6],
                    'message' => __d('wasabi_core', 'Ensure your password consists of at least 6 characters.')
                ]
            ])
            ->notEmpty('password_confirmation', __d('wasabi_core', 'Please repeat your Password.'), function ($context) {
                if ($context['newRecord'] === true) {
                    return true;
                }
                if (isset($context['data']['password']) && !empty($context['data']['password'])) {
                    return true;
                }
                return false;
            })
            ->add('password_confirmation', 'equalsPassword', [
                'rule' => function ($passwordConfirmation, $provider) {
                    if ($passwordConfirmation !== $provider['data']['password']) {
                        return __d('wasabi_core', 'The Password Confirmation does not match the Password field.');
                    }
                    return true;
                }
            ])
            ->add('language_id', 'isValid', [
                'rule' => function ($languageId) {
                    $languageIds = Hash::map(Configure::read('languages.backend'), '{n}', function ($language) {
                        return $language->id;
                    });
                    if (!in_array($languageId, $languageIds)) {
                        return __d('wasabi_core', 'Invalid language selected.');
                    }
                    return true;
                }
            ])
            ->add('groups', 'notEmpty', [
                'rule' => function ($groups) {
                    return !empty($groups['_ids'] ?? []);
                },
                'message' => __d('wasabi_core', 'Please select a group this user should belong to.')
            ])
            ->add('groups', 'isValid', [
                'rule' => function ($groups) {
                    if (empty($groups['_ids'])) {
                        return true;
                    }
                    $validGroupIds = $this->Groups->find()->extract('id')->toArray();
                    foreach ($groups['_ids'] as $groupId) {
                        if (!in_array($groupId, $validGroupIds)) {
                            return false;
                        }
                    }
                    return true;
                },
                'message' => __d('wasabi_core', 'Please select a group from the provided list.')
            ]);

        if (Wasabi::setting('Core.User.has_username')) {
            $validator->notEmpty('username', __d('wasabi_core', 'Please enter a username.'));
        }

        if (Wasabi::setting('Core.User.has_firstname_lastname')) {
            $validator->notEmpty('firstname', __d('wasabi_core', 'Please enter a first name.'));
            $validator->notEmpty('lastname', __d('wasabi_core', 'Please enter a last name.'));
        }

        if (Wasabi::setting('Core.User.allow_timezone_change')) {
            $validator->notEmpty('timezone', __d('wasabi_core', 'Please choose a time zone.'));
            $validator->add('timezone', 'isValid', [
                'rule' => function ($timezone) {
                    if (!in_array($timezone, DateTimeZone::listIdentifiers())) {
                        return __d('wasabi_core', 'Invalid timezone selected.');
                    }
                    return true;
                }
            ]);
        }

        return $validator;
    }

    /**
     * Only validate email.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     */
    public function validationEmailOnly(Validator $validator)
    {
        $validator
            ->notEmpty('email', __d('wasabi_core', 'Please enter an email address.'))
            ->add('email', [
                'email' => [
                    'rule' => 'email',
                    'message' => __d('wasabi_core', 'Please enter a valid email address.')
                ]
            ]);

        return $validator;
    }

    /**
     * Only validate password.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     */
    public function validationPasswordOnly(Validator $validator)
    {
        $validator
            ->notEmpty('password', __d('wasabi_core', 'Please enter a password.'))
            ->add('password', [
                'length' => [
                    'rule' => ['minLength', 6],
                    'message' => __d('wasabi_core', 'Ensure your password consists of at least 6 characters.')
                ]
            ])
            ->notEmpty('password_confirmation', __d('wasabi_core', 'Please repeat your Password.'), function ($context) {
                if ($context['newRecord'] === true) {
                    return true;
                }
                if (isset($context['data']['password']) && !empty($context['data']['password'])) {
                    return true;
                }
                return false;
            })
            ->add('password_confirmation', 'equalsPassword', [
                'rule' => function ($passwordConfirmation, $provider) {
                    if ($passwordConfirmation !== $provider['data']['password']) {
                        return __d('wasabi_core', 'The Password Confirmation does not match the Password field.');
                    }
                    return true;
                }
            ]);

        return $validator;
    }

    /**
     * Pre persistence rules.
     *
     * @param RulesChecker $rules The rules checker to customize.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add($rules->isUnique(['email'], __d('wasabi_core', 'This email is already used by another user.')))
            ->add($rules->isUnique(['username'], __d('wasabi_core', 'This username is already taken.')));
        return $rules;
    }

    /**
     * Called before request data is converted to an entity.
     *
     * @param Event $event An event instance.
     * @param ArrayObject $data The data to marshal.
     * @param ArrayObject $options Additional options.
     * @return void
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (isset($data['id'])) {
            // Unset password and password confirmation if password is empty when editing a user.
            if (isset($data['password']) && empty($data['password'])) {
                unset($data['password']);

                if (isset($data['password_confirmation'])) {
                    unset($data['password_confirmation']);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return User
     */
    public function newEntity($data = null, array $options = [])
    {
        return parent::newEntity($data, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return User
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        return parent::patchEntity($entity, $data, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|User
     */
    public function get($primaryKey, $options = [])
    {
        return parent::get($primaryKey, $options);
    }

    /**
     * Default finder for authenticated users.
     *
     * @param Query $query
     * @return Query
     */
    public function findAuthenticated(Query $query)
    {
        return $query
            ->select([
                'id',
                'language_id',
                'username',
                'firstname',
                'lastname',
                'email',
                'title',
                'timezone',
                'verified',
                'verified_at',
                'active',
                'activated_at'
            ]);
    }

    /**
     * Find verified users.
     *
     * @param Query $query
     * @return Query
     */
    public function findVerified(Query $query)
    {
        $query->where([
            'Users.verified' => true
        ]);
        return $query;
    }

    /**
     * Find unverified users.
     *
     * @param Query $query
     * @return Query
     */
    public function findNotVerified(Query $query)
    {
        $query->where([
            'Users.verified' => false
        ]);
        return $query;
    }

    /**
     * Find all active users.
     *
     * @param Query $query The query to decorate.
     * @return Query
     */
    public function findActive(Query $query)
    {
        $query->where([
            'Users.active' => true
        ]);
        return $query;
    }

    /**
     * Find all inactive users.
     *
     * @param Query $query The query to decorate.
     * @return Query
     */
    public function findInactive(Query $query)
    {
        $query->where([
            'Users.active' => false
        ]);
        return $query;
    }

    /**
     * Find all users awaiting activation by an admin.
     *
     * @param Query $query
     * @return Query
     */
    public function findAwaitingActivation(Query $query)
    {
        $query->where([
            'Users.activated_at IS' => null
        ]);
        return $query;
    }

    /**
     * Find users including their group name.
     *
     * @param Query $query The query to decorate.
     * @return Query
     */
    public function findWithGroupName(Query $query)
    {
        return $query
            ->select([
                'id',
                'username',
                'email',
                'verified',
                'active'
            ])
            ->contain([
                'Groups' => function (Query $q) {
                    return $q->select(['name']);
                }
            ]);
    }

    /**
     * Verify the given $user.
     *
     * @param User $user The user to verify.
     * @param bool $byAdmin Whether this action was initiated by an administrator or not.
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function verify(User $user, $byAdmin = false)
    {
        return $this->save($user->verify($byAdmin));
    }

    /**
     * Activate the given $user.
     *
     * @param User $user The user to activate.
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function activate(User $user)
    {
        return $this->save($user->activate());
    }

    /**
     * Deactivate the given $user.
     *
     * @param User $user The user to deactivate.
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function deactivate(User $user)
    {
        return $this->save($user->deactivate());
    }

    /**
     * Check if user with email exists and return the result
     *
     * @param string $email The email to check for.
     * @return User The User Entity or an empty Entity if none is found
     */
    public function existsWithEmail($email)
    {
        return $this->find()
            ->where([
                $this->alias() . '.email' => $email
            ])
            ->first();
    }

    /**
     * Find a user by unverified email address.
     *
     * @param string $email The email to check for.
     * @return mixed
     */
    public function getNotVerified($email)
    {
        return $this->find()
            ->where([
                $this->aliasField('email') => $email,
                $this->aliasField('verified') => 0
            ])
            ->first();
    }

    /**
     * Find all users that do not belong to a group.
     *
     * @return Query
     */
    public function findUsersWithNoGroup()
    {
        $query = $this->find();

        return $query->leftJoin(
                [$this->UsersGroups->alias() => $this->UsersGroups->table()],
                [$this->aliasField('id') . ' = ' . $this->UsersGroups->aliasField('user_id')]
            )
            ->select([
                'Users.id',
                'group_count' => $query->func()->count('UsersGroups.group_id')
            ])
            ->group('Users.id')
            ->having('group_count = 0');
    }

    /**
     * Get a user by $id including all user groups the user is assigned to.
     *
     * @param int|string $id The id of the user.
     * @return User
     */
    public function getUserAndGroups($id)
    {
        return $this->get($id, [
            'fields' => [
                $this->aliasField('id'),
                $this->aliasField('language_id'),
                $this->aliasField('firstname'),
                $this->aliasField('lastname'),
                $this->aliasField('username'),
                $this->aliasField('email'),
                $this->aliasField('timezone'),
                $this->aliasField('active'),
                $this->aliasField('verified'),
                $this->aliasField('verified_at')
            ],
            'contain' => [
                'Groups' => [
                    'queryBuilder' => function (Query $q) {
                        return $q->select([
                            $this->Groups->aliasField('id'),
                            $this->Groups->aliasField('name')
                        ]);
                    }
                ]
            ]
        ]);
    }
}
