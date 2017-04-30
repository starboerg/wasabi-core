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

use Cake\Cache\Cache;
use Cake\Database\Connection;
use Cake\Utility\Hash;
use Wasabi\Core\Model\Entity\GroupPermission;
use Wasabi\Core\Model\Table\GroupsTable;

/**
 * Class GroupPermissionsController
 *
 * @property GroupsTable Groups
 */
class PermissionsController extends BackendAppController
{
    /**
     * Holds existing group permissions in cache. Structure: group_id.path.enity
     *
     * @var array
     */
    protected $_existingGroupPermissions = [];

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->Guardian->loadPermissions();
        $this->loadModel('Wasabi/Core.Groups');
    }

    /**
     * Index action
     * GET
     *
     * @return void
     */
    public function index()
    {
        $groups = $this->Groups->find('list')->where(['id <>' => 1])->order(['id' => 'asc']);

        $permissions = $this->Groups->GroupPermissions->find('all')->combine(
            function ($entity) {
                return $this->Guardian->permissionManager->getPermissionId($entity->path);
            },
            'allowed',
            'group_id'
        )->toArray();

        foreach ($permissions as &$permission) {
            $permission = Hash::expand($permission);
        }

        $this->request->data['permissions'] = $permissions;

        $this->set([
            'superAdminGroup' => $this->Groups->find()->where(['id' => 1])->first(),
            'groups' => $groups,
            'permissions' => $this->Guardian->permissionManager->getPermissionGroups()
        ]);
    }

    /**
     * update action
     * POST
     *
     * @return void
     */
    public function update()
    {
        $groups = $this->Groups->find('list')->where(['id <>' => 1]);

        /** @var  Connection $ds */
        $ds = $this->Groups->GroupPermissions->getConnection();
        $ds->begin();

        if ($this->request->is('post') && !empty($this->request->getData())) {
            foreach ($groups as $groupId => $group) {
                if (!$ds->inTransaction()) {
                    continue;
                }
                $permissionData = Hash::flatten($this->request->getData('permissions.' . $groupId, []));
                foreach ($permissionData as $id => $allowed) {
                    if (!$ds->inTransaction()) {
                        continue;
                    }

                    $paths = $this->Guardian->permissionManager->getPermissionPaths($id);
                    foreach ($paths as $path) {
                        if (!$ds->inTransaction()) {
                            continue;
                        }

                        /** @var GroupPermission $existingGroupPermission */
                        if (($existingGroupPermission = $this->_getExistingGroupPermission($groupId, $path)) !== false) {
                            // existing entity
                            if ($existingGroupPermission->allowed !== (int)$allowed) {
                                $existingGroupPermission->set('allowed', (int)$allowed);
                            }
                            $groupPermission = $existingGroupPermission;
                        } else {
                            // new entity
                            $groupPermission = $this->Groups->GroupPermissions->newEntityFor($groupId, $path, (int)$allowed);
                            $this->_existingGroupPermissions[$groupId][$path] = $groupPermission;
                        }

                        if (!$this->Groups->GroupPermissions->save($groupPermission)) {
                            $ds->rollback();
                        }
                    }
                }
            }
        }

        if ($ds->inTransaction()) {
            $ds->commit();
            Cache::clear(false, 'wasabi/core/group_permissions');
            $this->Flash->success(__d('wasabi_core', 'Permissions have been updated.'));
        } else {
            $this->Flash->error(__d('wasabi_core', 'An error occurred while updating the permissions. Please try again.'));
        }

        $this->redirect(['action' => 'index']);
    }

    /**
     * Get an existing group permission for the given $groupId and $path.
     *
     * @param int $groupId The group id.
     * @param string $path The permission path.
     * @return bool|GroupPermission
     */
    protected function _getExistingGroupPermission($groupId, $path)
    {
        if (empty($this->_existingGroupPermissions)) {
            $this->_existingGroupPermissions = $this->Groups->GroupPermissions->findAllExisting()->toArray();
        }

        if (!isset($this->_existingGroupPermissions[$groupId]) ||
            !isset($this->_existingGroupPermissions[$groupId][$path])
        ) {
            return false;
        }

        return $this->_existingGroupPermissions[$groupId][$path];
    }
}
