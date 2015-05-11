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

    /**
     * Find all permissions for a specific $groupId.
     *
     * @param string $groupId
     * @return array|mixed
     */
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
     * Get all permissions paths (Plugin.Controller.action) for a specific $groupId.
     *
     * @param string $groupId
     * @return array of permission paths
     */
    public function getAllPermissionPathsForGroup($groupId)
    {
        $groupPermissions = $this->find('all')
            ->select('path')
            ->where(['group_id' => $groupId])
            ->hydrate(false)
            ->toArray();

        return Hash::extract($groupPermissions, '{n}.path');
    }

    /**
     * Create all missing permissions for a specific $groupId and the
     * supplied $actionMap.
     *
     * @param string $groupId
     */
    public function createMissingPermissions($groupId, array $actionMap)
    {
        $existingPaths = $this->getAllPermissionPathsForGroup($groupId);

        $missingPaths = array_diff(array_keys($actionMap), $existingPaths);

        if (empty($missingPaths)) {
            return;
        }

        $this->connection()->transactional(function () use ($missingPaths, $actionMap, $groupId) {
            foreach ($missingPaths as $missingPath) {
                $action = $actionMap[$missingPath];
                $this->save(
                    new GroupPermission([
                        'group_id' => $groupId,
                        'path' => $missingPath,
                        'plugin' => $action['plugin'],
                        'controller' => $action['controller'],
                        'action' => $action['action'],
                    ])
                );
            }
        });
    }

    /**
     * Delete all permission for a $groupId for paths (Plugin.Controller.action)
     * that are no longer present in the codebase.
     *
     * @param string $groupId
     */
    public function deleteOrphans($groupId, array $actionMap)
    {
        $groupPermissions = Hash::extract(
            $this->find('all')
                ->where(['group_id' => $groupId])
                ->hydrate(false)
                ->toArray(),
            '{n}.path');

        $orphans = array_diff($groupPermissions, array_keys($actionMap));

        if (!empty($orphans)) {
            $this->deleteAll([
                'group_id' => $groupId,
                'path IN' => $orphans
            ]);
        }
    }
}
