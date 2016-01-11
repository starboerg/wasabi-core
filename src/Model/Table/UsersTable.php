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
namespace Wasabi\Core\Model\Table;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use DateTimeZone;
use Wasabi\Core\Model\Entity\User;

/**
 * Class UsersTable
 * @property GroupsTable Groups
 * @package Wasabi\Core\Model\Table
 */
class UsersTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Groups', [
            'className' => 'Wasabi/Core.Groups'
        ]);

        $this->hasMany('Tokens', [
            'className' => 'Wasabi/Core.Tokens'
        ]);

        $this->addBehavior('CounterCache', ['Groups' => ['user_count']]);
        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        return $validator
            ->notEmpty('username', __d('wasabi_core', 'Please enter a username.'))
            ->notEmpty('email', __d('wasabi_core', 'Please enter an email address.'))
            ->add('email', [
                'email' => [
                    'rule' => 'email',
                    'message' => __d('wasabi_core', 'Please enter a valid email address.')
                ]
            ])
            ->notEmpty('group_id', __d('wasabi_core', 'Please select a group this user belongs to.'))
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
            ->add('timezone', 'isValid', [
                'rule' => function ($timezone) {
                    if (!in_array($timezone, DateTimeZone::listIdentifiers())) {
                        return __d('wasabi_core', 'Invalid timezone selected.');
                    }
                    return true;
                }
            ]);
    }

    /**
     * Only validate email.
     *
     * @param Validator $validator
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
     * @param Validator $validator
     * @return Validator
     */
    public function validationPasswordOnly(Validator $validator)
    {
        $validator
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
            ]);

        return $validator;
    }

    /**
     * Pre persistence rules.
     *
     * @param RulesChecker $rules
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email'], __d('wasabi_core', 'This email is already used by another user.')));
        return $rules;
    }

    /**
     * Called before request data is converted to an entity.
     *
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
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
     * Find all active users.
     *
     * @param Query $query
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
     * Find users including their group name.
     *
     * @param Query $query
     * @return $this|array
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
     * @param User $user
     * @param bool $byAdmin
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function verify(User $user, $byAdmin = false)
    {
        return $this->save($user->verify($byAdmin));
    }

    /**
     * Activate the given $user.
     *
     * @param User $user
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function activate(User $user)
    {
        return $this->save($user->activate());
    }

    /**
     * Deactivate the given $user.
     *
     * @param User $user
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function deactivate(User $user)
    {
        return $this->save($user->deactivate());
    }

    /**
     * Check if user with email exists and return the result
     *
     * @param $email
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
}
