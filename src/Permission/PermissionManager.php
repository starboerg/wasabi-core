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
namespace Wasabi\Core\Permission;

use Cake\Collection\Collection;
use Cake\Core\Exception\Exception;
use Cake\Utility\Text;

class PermissionManager
{
    /**
     * Holds the permission groups.
     *
     * @var Collection
     */
    protected $_permissionGroups;

    /**
     * Holds all registered permission group ids.
     *
     * @var array
     */
    protected $_permissionGroupIds;

    /**
     * Holds a mapping of all permission paths to their corresponding permission id.
     *
     * @var array
     */
    protected $_mapPathToId;

    /**
     * Holds a mapping of all permission ids to their corresponding permission path.
     *
     * @var array
     */
    protected $_mapIdToPath;

    /**
     * PermissionManager constructor.
     */
    public function __construct()
    {
        $this->_permissionGroups = new Collection([]);
        $this->_permissionGroupIds = [];
    }

    /**
     * Add the given permission group to the set of permission groups.
     *
     * @param PermissionGroup $permissionGroup
     * @return PermissionManager
     */
    public function addPermissionGroup(PermissionGroup $permissionGroup)
    {
        if (in_array($permissionGroup->getId(), $this->_permissionGroupIds)) {
            throw new Exception(Text::insert('PermissionGroup :id is already registered.', ['id' => $permissionGroup->getId()]));
        }

        $this->_permissionGroups = $this->_permissionGroups->append([$permissionGroup]);
        $this->_permissionGroupIds[] = $permissionGroup->getId();

        return $this;
    }

    /**
     * Add multiple permission groups.
     *
     * @param PermissionGroup[] $permissionGroups
     * @return PermissionManager
     */
    public function addPermissionGroups(array $permissionGroups)
    {
        foreach ($permissionGroups as $permissionGroup) {
            $this->addPermissionGroup($permissionGroup);
        }

        return $this;
    }

    /**
     * Remove the given permission group from the set of permission groups.
     *
     * @param PermissionGroup $permissionGroup
     * @return PermissionManager
     */
    public function remove(PermissionGroup $permissionGroup)
    {
        if (!in_array($permissionGroup->getId(), $this->_permissionGroupIds)) {
            throw new Exception(Text::insert('No PermissionGroup with id :id could be found.', ['id' => $permissionGroup->id()]));
        }

        $permissions = $this->_permissionGroups->reject(function (PermissionGroup $group) use ($permissionGroup) {
            return $group->getId() === $permissionGroup->getId();
        });

        $this->_permissionGroups = $permissions;

        return $this;
    }

    /**
     * Create a new permission instance.
     *
     * @param integer $priority
     * @param string $id
     * @param string $name
     * @param array $paths
     * @return Permission
     * @throws \Aura\Intl\Exception
     * @throws \ReflectionException
     */
    public function createPermission($priority, $id, $name, array $paths)
    {
        return (new Permission)
            ->setPriority($priority)
            ->setId($id)
            ->setName($name)
            ->setPaths($paths);
    }

    /**
     * Get all registered permission groups.
     *
     * @param boolean $ordered Whether the permission groups should be ordered by priority.
     * @return Collection
     */
    public function getPermissionGroups($ordered = true)
    {
        if ($ordered === true) {
            return $this->_orderedPermissionGroups();
        }

        return $this->_permissionGroups;
    }

    /**
     * Get the permission id for the given permission path.
     *
     * @param string $path The permission path.
     * @return bool|string
     */
    public function getPermissionId($path)
    {
        if (empty($this->_mapPathToId)) {
            $this->getPermissionGroups(false)->each(function (PermissionGroup $permissionGroup) {
                $permissionGroup->getPermissions(false)->each(function (Permission $permission) {
                    foreach ($permission->getPaths() as $path) {
                        $this->_mapPathToId[$path] = $permission->getId();
                    }
                });
            });
        }

        if (!isset($this->_mapPathToId[$path])) {
            return false;
        }

        return $this->_mapPathToId[$path];
    }

    /**
     * Get the 'paths' from $this->_permissions for the provided $id.
     *
     * @param string $id The permission id.
     * @return bool|array
     */
    public function getPermissionPaths($id)
    {
        if (empty($this->_mapIdToPath)) {
            $this->getPermissionGroups(false)->each(function (PermissionGroup $permissionGroup) {
                $permissionGroup->getPermissions(false)->each(function (Permission $permission) {
                    $this->_mapIdToPath[$permission->getId()] = $permission->getPaths();
                });
            });
        }

        if (!isset($this->_mapIdToPath[$id])) {
            return false;
        }

        $paths = $this->_mapIdToPath[$id];
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        return $paths;
    }

    /**
     * Get all permission groups ordered by priority.
     *
     * @return Collection
     */
    protected function _orderedPermissionGroups()
    {
        return $this->_permissionGroups->sortBy(function (PermissionGroup $permissionGroup) {
            return $permissionGroup->getPriority();
        }, SORT_ASC)->compile();
    }
}
