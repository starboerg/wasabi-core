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
 * @property int $group_id
 * @property int $language_id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property bool $verified
 * @property bool $active
 * @property DateTime $verified_at
 * @property DateTime $activated_at
 * @property DateTime $created
 * @property DateTime $modified
 *
 * @property Media[] $media
 */
class User extends Entity
{
    /**
     * Accessible fields.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    /**
     * Holds the initialized user permissions.
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Always hash the users password on save calls.
     *
     * @param string $password
     * @return string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    /**
     * Verify user
     *
     * @param bool $byAdmin
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
     * @param array $url
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
}
