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

use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Wasabi\Core\Model\Entity\Group;

/**
 * Class GroupsTable
 *
 * @property UsersTable $Users
 * @property GroupPermissionsTable $GroupPermissions
 * @property UsersGroupsTable $UsersGroupsTable
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
        if (Configure::read('Wasabi.User.belongsToManyGroups')) {
            $this->belongsToMany('Users', [
                'className' => 'Wasabi/Core.Users',
                'through' => 'Wasabi/Core.UsersGroups'
            ]);

            $this->hasMany('UsersGroups', [
                'className' => 'Wasabi/Core.UsersGroups'
            ]);
        } else {
            $this->hasMany('Users', [
                'className' => 'Wasabi/Core.Users'
            ]);
        }

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
     * Move users from one group to another. (Used when a user may only belong to a single group.) 
     *
     * @param Group $groupFrom A group instance.
     * @param Group $groupTo A group instance.
     * @return int number of affected user rows
     */
    public function moveUsersToAlternativeGroup($groupFrom, $groupTo)
    {
        $affectedUsers = $this->Users->updateAll(['group_id' => $groupTo->id], ['group_id' => $groupFrom->id]);
        if ($affectedUsers > 0) {
            // manually update group counter cache
            $subFromUserCountExpression = new QueryExpression('user_count = user_count - ' . $affectedUsers);
            $this->updateAll([$subFromUserCountExpression], ['id' => $groupFrom->id]);
            $addToUserCountExpression = new QueryExpression('user_count = user_count + ' . $affectedUsers);
            $this->updateAll([$addToUserCountExpression], ['id' => $groupTo->id]);
        }
        return $affectedUsers;
    }

    /**
     * Assign users to new groups. (Used when a user may belong to multiple groups.) 
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
