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
namespace Wasabi\Core\Policy;

use Wasabi\Core\Model\Entity\Group;
use Wasabi\Core\Model\Entity\User;

class GroupPolicy
{
    /**
     * Determine if the given group can be deleted by the currently logged in user.
     *
     * @param User $user The currently logged in user.
     * @param Group $group The group to delete.
     * @return boolean
     */
    public function delete(User $user, Group $group)
    {
        // The super admin group cannot be deleted.
        if ($group->id === 1) {
            return false;
        }

        // A user cannot delete a group he belongs to.
        if (in_array($group->id, $user->group_id)) {
            return false;
        }

        return true;
    }
}
