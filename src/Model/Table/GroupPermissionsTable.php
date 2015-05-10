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

use Cake\Cache\Cache;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Wasabi\Core\Model\Entity\GroupPermission;

/**
 * Class GroupPermissionsTable
 * @property GroupsTable Groups
 * @package Wasabi\Core\Model\Table
 */
class GroupPermissionsTable extends Table
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

        $this->addBehavior('Timestamp');
    }

    public function findAllForGroup($groupId)
    {
        if (!$groupId) {
            return [];
        }

        $permissions = Cache::remember($groupId, function () use ($groupId) {
            return $this
                ->find('all')
                ->select(['path'])
                ->where([
                    'group_id' => $groupId,
                    'allowed' => true
                ])
                ->hydrate(false);
        }, 'wasabi/core/group_permissions');

        return $permissions;
    }

    /**
     * @param array $group
     * @param array $actionMap
     */
    public function createMissingPermissions(array $group, array $actionMap)
    {
        $groupPermissions = Hash::extract(
            $this->find('all')
                ->where(['group_id' => $group['id']])
                ->hydrate(false)
                ->toArray(),
            '{n}.path');

        $missingGroupPermissions = array_diff(array_keys($actionMap), $groupPermissions);

        if (!empty($missingGroupPermissions)) {
            $this->connection()->transactional(function () use ($missingGroupPermissions, $actionMap, $group) {
                foreach ($missingGroupPermissions as $missingPath) {
                    $action = $actionMap[$missingPath];
                    if (!$this->save(new GroupPermission([
                            'group_id' => $group['id'],
                            'path' => $missingPath,
                            'plugin' => $action['plugin'],
                            'controller' => $action['controller'],
                            'action' => $action['action'],
                        ])
                    )
                    ) {
                        $this->connection()->rollback();
                    }
                }
            });
        }
    }

    /**
     *
     */
    public function deleteOrphans()
    {
        $groups = $this->Groups->find('list', [
            'keyField' => 'id',
            'valueField' => 'id'
        ])->toArray();

        if (!$this->deleteAll([
            'group_id NOT IN' => $groups
        ])
        ) {
            $this->connection()->rollback();
        }
    }
}
