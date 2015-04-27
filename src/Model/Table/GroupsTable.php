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

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Wasabi\Core\Model\Entity\Group;

/**
 * Class GroupsTable
 * @property UsersTable Users
 * @package Wasabi\Core\Model\Table
 */
class GroupsTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->hasMany('Users', [
            'className' => 'Wasabi/Core.Users'
        ]);
        $this->hasMany('GroupPermissions', [
            'className' => 'Wasabi/Core.GroupPermissions'
        ]);

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
        $validator->notEmpty('name', __d('wasabi_core', 'Please enter a name for the group.'));
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
        $rules->add($rules->isUnique(['name'], __d('wasabi_core', 'Another group with this name already exists.')));
        return $rules;
    }

    /**
     * Move users from one group to another.
     *
     * @param Group $groupFrom
     * @param Group $groupTo
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
}
