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

use Cake\Datasource\EntityInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Wasabi\Core\Model\Entity\Group;

/**
 * Class GroupsTable
 *
 * @property UsersTable $Users
 * @property GroupPermissionsTable $GroupPermissions
 * @property UsersGroupsTable $UsersGroups
 */
class GroupsTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->belongsToMany('Users', [
            'className' => 'Wasabi/Core.Users',
            'through' => 'Wasabi/Core.UsersGroups'
        ]);

        $this->hasMany('UsersGroups', [
            'className' => 'Wasabi/Core.UsersGroups'
        ]);

        $this->hasMany('GroupPermissions', [
            'className' => 'Wasabi/Core.GroupPermissions',
            'dependent' => true
        ]);

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator The validator to customize.
     * @return Validator
     * @throws \Aura\Intl\Exception
     */
    public function validationDefault(Validator $validator)
    {
        $validator->notEmpty('name', __d('wasabi_core', 'Please enter a name for the group.'));
        $validator->notEmpty('description', __d('wasabi_core', 'Please enter a description for the group.'));
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
        $rules->add($rules->isUnique(['name'], __d('wasabi_core', 'Another group with this name already exists.')));
        return $rules;
    }

    /**
     * {@inheritdoc}
     *
     * @return Group
     */
    public function newEntity($data = null, array $options = [])
    {
        return parent::newEntity($data, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return Group
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        return parent::patchEntity($entity, $data, $options);
    }

    /**
     * {@inheritDoc}
     *
     * @return array|Group
     */
    public function get($primaryKey, $options = [])
    {
        return parent::get($primaryKey, $options);
    }

    /**
     * Assign users to new groups.
     *
     * @param array $userIdGroupIdMapping A mapping of user ids to their new group ids.
     * @return int number of affected user rows
     */
    public function moveUsersToAlternativeGroups($userIdGroupIdMapping)
    {
        $affectedUsers = 0;
        $affectedGroups = [];
        foreach ($userIdGroupIdMapping as $userId => $groupId) {
            $affectedUsers += $this->UsersGroups->updateAll([
                'group_id' => $groupId
            ], [
                'user_id' => $userId
            ]);
            if (!in_array($groupId, $affectedGroups)) {
                $affectedGroups[] = $groupId;
            }
        }
        if ($affectedUsers > 0 && !empty($affectedGroups)) {
            $this->updateUserCount($affectedGroups);
        }
        return $affectedUsers;
    }

    /**
     * Update the user_count counter cache for the given $groupIds.
     *
     * @param array $groupIds An array of group ids whose user_count should be updated.
     * @return void
     */
    public function updateUserCount($groupIds)
    {
        $query = $this->UsersGroups->find();
        $groupIdUserCount = $query
            ->select(['group_id', 'user_count' => $query->func()->count('user_id')])
            ->where(['group_id IN' => $groupIds])->group('group_id')
            ->combine('group_id', 'user_count')->toArray();

        foreach ($groupIds as $groupId) {
            $userCount = isset($groupIdUserCount[$groupId]) ? $groupIdUserCount[$groupId] : 0;
            $this->save($this->get($groupId)->set('user_count', $userCount));
        }
    }
}
