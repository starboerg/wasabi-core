<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $u user
 */
?><tr<?= $class ?>>
    <td class="col-id center"><?= $u['id'] ?></td>
    <td class="col-username"><?= $this->Guardian->protectedLink($u['username'], '/backend/users/edit/' . $u['id'], ['title' => __d('wasabi_core', 'Edit User')], true) ?></td>
    <td class="col-email"><?= $u['email'] ?></td>
    <td class="col-group-name"><?= $u['group']['name'] ?></td>
    <td class="col-status"><?php
        $isCurrentUser = ($this->request->session()->read('Auth.User.id') === $u['id']);
        if ($u['verified'] === false) {
            echo $this->Guardian->protectedConfirmationLink(
                '<span class="label">' . __d('wasabi_core', 'not verified') . '</span>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'verify',
                    $u['id']
                ],
                [
                    'escape' => false,
                    'confirm-message' => __d('wasabi_core', 'Verify user <strong>{0}</strong>?', $u['username']),
                    'confirm-title' => __d('wasabi_core', 'Verify User'),
                    'title' => __d('wasabi_core', 'Manually verify the user\'s email address.'),
                    'ajax' => true,
                    'notify' => 'table.users',
                    'event' => 'verify',
                    'data-user-id' => $u['id']
                ],
                true
            );
        } else {
            echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'The user has verified his email address.') . '">' . __d('wasabi_core', 'verified') . '</span>';
        }
        echo ' ';
        if ($u['active'] === false) {
            echo $this->Guardian->protectedConfirmationLink(
                '<span class="label">' . __d('wasabi_core', 'inactive') . '</span>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'activate',
                    $u['id']
                ],
                [
                    'escape' => false,
                    'confirm-message' => __d('wasabi_core', 'Activate user <strong>{0}</strong>?', $u['username']),
                    'confirm-title' => __d('wasabi_core', 'Activate User'),
                    'title' => __d('wasabi_core', 'Activate user &quot;{0}&quot;', $u['username']),
                    'ajax' => true,
                    'notify' => 'table.users',
                    'event' => 'activate',
                    'data-user-id' => $u['id']
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
                        $u['id']
                    ],
                    [
                        'escape' => false,
                        'confirm-message' => __d('wasabi_core', 'Deactivate user <strong>{0}</strong>?', $u['username']),
                        'confirm-title' => __d('wasabi_core', 'Deactivate User'),
                        'title' => __d('wasabi_core', 'Deactivate user &quot;{0}&quot;', $u['username']),
                        'ajax' => true,
                        'notify' => 'table.users',
                        'event' => 'deactivate',
                        'data-user-id' => $u['id']
                    ],
                    true
                );
            }
        }
    ?></td>
    <td class="col-actions center"><?php
        if ($u['id'] != $this->request->session()->read('User.id') && $u['id'] != 1) {
            echo $this->Guardian->protectedConfirmationLink('<i class="wicon-remove"></i>', '/backend/users/delete/' . $u['id'], [
                'class' => 'action-delete',
                'title' => __d('wasabi_core', 'Delete User'),
                'confirm-message' => __d('wasabi_core', 'Do you really want to delete user <strong>{0}</strong>?', $u['username']),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                'escape' => false
            ]);
        } else {
            echo '-';
        }
    ?></td>
</tr>