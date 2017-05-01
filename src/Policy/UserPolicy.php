<?php

namespace Wasabi\Core\Policy;

use Wasabi\Core\Model\Entity\User;

class UserPolicy
{
    /**
     * Determine if the given user can be deleted by the currently logged in user.
     *
     * @param User $currentUser The currently logged in user.
     * @param User $user The user to update.
     * @return boolean
     */
    public function delete(User $currentUser, User $user)
    {
        // The admin user account cannot be deleted.
        if ($user->id === 1) {
            return false;
        }

        // A user cannot delete his own account.
        if ($currentUser->id === $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the given user account can be deactivated by the currently logged in user.
     *
     * @param User $currentUser The currently logged in user.
     * @param User $user The user account to deactivate.
     * @return boolean
     */
    public function deactivate(User $currentUser, User $user)
    {
        // The user cannot deactivate his own account.
        if ($currentUser->id === $user->id) {
            return false;
        }

        return true;
    }
}
