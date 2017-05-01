<?php

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
