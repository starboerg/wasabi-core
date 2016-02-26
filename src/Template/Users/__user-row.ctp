<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */
?><tr>
    <td class="col-id center"><?= $user->id ?></td>
    <td class="col-username"><?= $this->Guardian->protectedLink($user->username, '/backend/users/edit/' . $user->id, ['title' => __d('wasabi_core', 'Edit User')], true) ?></td>
    <td class="col-email"><?= $user->email ?></td>
    <td class="col-group-name"><?= $user->group->name ?></td>
    <td class="col-status"><?php
        $isCurrentUser = ($this->request->session()->read('Auth.User.id') === $user->id);
        if ($user->verified === false) {
            echo $this->Guardian->protectedConfirmationLink(
                '<span class="label">' . __d('wasabi_core', 'not verified') . '</span>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'verify',
                    $user->id
                ],
                [
                    'title' => __d('wasabi_core', 'Manually verify the user\'s email address.'),
                    'confirm-title' => __d('wasabi_core', 'Verify Email'),
                    'confirm-message' => __d('wasabi_core', 'Do you really want to verify the email address of user <strong>{0}</strong>?', $user->username),
                    'escape' => false
                ],
                true
            );
        } else {
            echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'The user has verified his email address.') . '">' . __d('wasabi_core', 'verified') . '</span>';
        }
        echo ' ';
        if ($user->active === false) {
            echo $this->Guardian->protectedConfirmationLink(
                '<span class="label">' . __d('wasabi_core', 'inactive') . '</span>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'activate',
                    $user->id
                ],
                [
                    'title' => __d('wasabi_core', 'Activate user &quot;{0}&quot;', $user->username),
                    'confirm-title' => __d('wasabi_core', 'Activate User'),
                    'confirm-message' => __d('wasabi_core', 'Activate user <strong>{0}</strong>?', $user->username),
                    'escape' => false
                ],
                true
            );
        } else {
            if ($isCurrentUser) {
                echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'You cannot deactivate your own account.') . '">' . __d('wasabi_core', 'active') . '</span>';
            } else {
                echo $this->Guardian->protectedConfirmationLink(
                    '<span class="label label--success">' . __d('wasabi_core', 'active') . '</span>',
                    [
                        'plugin' => 'Wasabi/Core',
                        'controller' => 'Users',
                        'action' => 'deactivate',
                        $user->id
                    ],
                    [
                        'title' => __d('wasabi_core', 'Deactivate user &quot;{0}&quot;', $user->username),
                        'confirm-title' => __d('wasabi_core', 'Deactivate User'),
                        'confirm-message' => __d('wasabi_core', 'Deactivate user <strong>{0}</strong>?', $user->username),
                        'escape' => false
                    ],
                    true
                );
            }
        }
    ?></td>
    <td class="col-actions center"><?php
        if ($user->id != $this->request->session()->read('User.id') && $user->id != 1) {
            echo $this->Guardian->protectedConfirmationLink('<i class="wicon-remove"></i>', '/backend/users/delete/' . $user->id, [
                'class' => 'action-delete',
                'title' => __d('wasabi_core', 'Delete User'),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                'confirm-message' => __d('wasabi_core', 'Do you really want to delete user <strong>{0}</strong>?', $user->username),
                'escape' => false
            ]);
        } else {
            echo '-';
        }
    ?></td>
</tr>
