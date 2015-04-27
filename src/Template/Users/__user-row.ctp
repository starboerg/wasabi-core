<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $u user
 */
?><tr<?= $class ?>>
    <td class="center"><?= $u['id'] ?></td>
    <td><?= $this->Guardian->protectedLink($u['username'], '/backend/users/edit/' . $u['id'], ['title' => 'Benutzer bearbeiten'], true) ?></td>
    <td><?= $u['email'] ?></td>
    <td><?= $u['group']['name'] ?></td>
    <td><?php
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
                    'confirm-message' => __d('wasabi_core', 'Activate user <strong>{0}</strong>?', $u['username']),
                    'confirm-title' => __d('wasabi_core', 'Activate User'),
                    'title' => __d('wasabi_core', 'Manually verify the user\'s email address.'),
                    'ajax' => true,
                    'notify' => 'table.users',
                    'event' => 'activate',
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
    <td class="actions center"><?php
        if ($u['id'] != $this->request->session()->read('User.id') && $u['id'] != 1) {
            echo $this->Guardian->protectedConfirmationLink('<i class="icon-delete"></i>', '/backend/users/delete/' . $u['id'], [
                'class' => 'action-delete',
                'title' => 'Diesen Benutzer löschen',
                'confirm-message' => __d('wasabi_core', 'Benutzer <strong>{0}</strong> wirklich löschen?', $u['username']),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                'escape' => false
            ]);
        } else {
            echo '-';
        }
    ?></td>
</tr>