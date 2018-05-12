<?php

namespace Wasabi\Core\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * Class UsersGroupsTable
 *
 * @property UsersTable $Users
 * @property GroupsTable $Groups
 */
class UsersGroupsTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config The configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Users', [
            'className' => 'Wasabi/Core.Users'
        ]);
        $this->belongsTo('Groups', [
            'className' => 'Wasabi/Core.Groups'
        ]);

        $this->addBehavior('CounterCache', ['Groups' => ['user_count']]);
    }

    /**
     * Get all group ids for user $userId.
     *
     * @param int|string $userId The user id to get group ids for.
     * @return array
     */
    public function getGroupIds($userId)
    {
        $groups = $this->find()
            ->select(['group_id'])
            ->where(['user_id' => $userId])
            ->enableHydration(false)
            ->toArray();

        return Hash::extract($groups, '{n}.group_id');
    }

    /**
     * Find all user ids of users assigned to only one group.
     *
     * @return array
     */
    public function findUserIdsWithOnlyOneGroup()
    {
        $query = $this->find();
        return $query
            ->select([
                'user_id',
                'group_count' => $query->func()->count('*')
            ])
            ->group('user_id')
            ->having([
                'group_count = 1'
            ])
            ->extract('user_id')
            ->toArray();
    }
}
