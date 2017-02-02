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
namespace Wasabi\Core\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Entity;
use DateTime;
use Wasabi\Core\Wasabi;

/**
 * Class User
 * @package Wasabi\Core\Model\Entity
 *
 * @property int $id
 * @property int $language_id
 * @property string $firstname
 * @property string $lastname
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $timezone
 * @property bool $verified
 * @property DateTime $verified_at
 * @property bool $active
 * @property DateTime $activated_at
 * @property DateTime $created
 * @property DateTime $modified
 * @property Media[] $media
 * @property Group[] $groups
 *
 */
class User extends Entity
{
    /**
     * Holds the initialized user permissions.
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Get the full name of the user.
     *
     * @param bool $lastNameFirst Whether to display the lastname at the first position or not.
     * @return string
     */
    public function fullName($lastNameFirst = false)
    {
        if (!Wasabi::setting('Core.User.has_firstname_lastname')) {
            return $this->username;
        }
        if ($lastNameFirst) {
            return $this->lastname . ', ' . $this->firstname;
        }
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Get an array of group names the user belongs to.
     *
     * @return array
     */
    public function getGroupNames()
    {
        if (empty($this->groups)) {
            return [];
        }

        $result = [];
        foreach ($this->groups as $group) {
            $result[] = $group->name;
        }

        return $result;
    }

    /**
     * Verify user
     *
     * @param bool $byAdmin True if the user is verified by an administrator.
     * @return User $this
     */
    public function verify($byAdmin = false)
    {
        $this->verified_at = date('Y-m-d H:i:s');
        $this->verified = true;

        EventManager::instance()->dispatch(new Event('Wasabi.User.verified' . (($byAdmin) ? 'ByAdmin' : ''), $this));

        return $this;
    }

    /**
     * Activate user
     *
     * @return User $this
     */
    public function activate()
    {
        $this->activated_at = date('Y-m-d H:i:s');
        $this->active = true;

        EventManager::instance()->dispatch(new Event('Wasabi.User.activated', $this));

        return $this;
    }

    /**
     * Deactivate user
     *
     * @return User $this
     */
    public function deactivate()
    {
        $this->activated_at = null;
        $this->active = false;

        EventManager::instance()->dispatch(new Event('Wasabi.User.deactivated', $this));

        return $this;
    }

    /**
     * Returns the access level of the user for the given plugin controller action path.
     *
     * @param array $url The url to get the access level for.
     * @return int|bool
     */
    public function getAccessLevel($url = null)
    {
        if ($url === null) {
            $url = Wasabi::getCurrentUrlArray();
        }

        $path = guardian()->getPathFromUrl($url);

        if (!array_key_exists($path, $this->permissions)) {
            return 0;
        }

        return $this->permissions[$path];
    }

    /**
     * Determine if the user has the provided access level.
     *
     * @param mixed $permissionConstant e.g. Permission::OWN
     * @return bool
     */
    public function hasAccessLevel($permissionConstant)
    {
        return ($this->getAccessLevel() === $permissionConstant['value']);
    }

    /**
     * Always hash the users password on save calls.
     *
     * @param string $password The password to hash.
     * @return string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
}
