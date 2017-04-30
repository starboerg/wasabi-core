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

class PermissionGroup
{
    /**
     * The identifier of this permission group.
     *
     * @var string
     */
    protected $_id;

    /**
     * The name of this permission group.
     *
     * @var string
     */
    protected $_name;

    /**
     * Holds the permissions for this permission group.
     *
     * @var Collection
     */
    protected $_permissions;

    /**
     * Holds all registered permission ids.
     *
     * @var array
     */
    protected $_permissionIds;

    /**
     * The priority of this permission group in a set of several permission groups.
     *
     * @var integer
     */
    protected $_priority;

    /**
     * PermissionGroup constructor.
     */
    public function __construct()
    {
        $this->_permissions = new Collection([]);
        $this->_permissionIds = [];
        $this->_priority = 999999;
    }

    /**
     * Get the id of this permission group.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the id of this permission group.
     *
     * @param string $id
     * @return PermissionGroup
     */
    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Get the name of this permission group.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the name of this permission group.
     *
     * @param String $name
     * @return PermissionGroup
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get the priority of this permission group.
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Set the priority of this permission group.
     *
     * @param integer $priority
     * @return PermissionGroup
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;

        return $this;
    }

    /**
     * Get the permissions.
     *
     * @param boolean $ordered
     * @return Collection
     */
    public function getPermissions($ordered = true)
    {
        if ($ordered === true) {
            return $this->_orderedPermissions();
        }

        return $this->_permissions;
    }

    /**
     * Add a permission to this permission group.
     *
     * @param Permission $permission
     * @return PermissionGroup
     */
    public function addPermission(Permission $permission)
    {
        if (in_array($permission->getId(), $this->_permissionIds)) {
            throw new Exception(Text::insert('A permission with the id ":id" already exists in permission group ":permissionGroupId".', [
                'id' => $permission->getId(),
                'permissionGroupId' => $this->getId()
            ]));
        }

        $this->_permissions = $this->_permissions->append([$permission]);
        $this->_permissionIds[] = $permission->getId();

        return $this;
    }

    /**
     * Add the given permissions to this permission group.
     *
     * @param Permission[] $permissions
     * @return PermissionGroup
     */
    public function addPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }

        return $this;
    }


    /**
     * Remove the permission identified by $permissionId from the set of permissions.
     *
     * @param string $permissionId The permission id of the permission to remove.
     * @return $this
     */
    public function removePermission($permissionId)
    {
        if (!in_array($permissionId, $this->_permissionIds)) {
            throw new Exception(Text::insert('PermissionGroup ":permissionGroupId" does not contain a permission with id ":id"', [
                'id' => $permissionId,
                'permissionGroupId' => $this->getId()
            ]));
        }

        $this->_permissions->reject(function (Permission $permission) use ($permissionId) {
            return $permissionId === $permission->getId();
        });
        $this->_permissionIds = array_diff($this->_permissionIds, array($permissionId));

        return $this;
    }

    /**
     * Order the currently set permissions by priority.
     *
     * @return Collection
     */
    protected function _orderedPermissions()
    {
        return $this->_permissions->sortBy(function (Permission $permission) {
            return $permission->getPriority();
        }, SORT_ASC)->compile();
    }
}
