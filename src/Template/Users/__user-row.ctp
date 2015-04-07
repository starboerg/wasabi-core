<tr<?= $class ?>>
    <td class="center"><?= $u['id'] ?></td>
    <td>
        <?php
        if ($u['id'] == 1 && $this->request->session()->read('User.id') != 1) {
            echo $u['username'];
        } else {
            echo $this->Guardian->protectedLink($u['username'], '/backend/users/edit/' . $u['id'], ['title' => 'Benutzer bearbeiten'], true);
        }
        ?>
    </td>
    <td><?= $u['email'] ?></td>
    <td><?= $u['group']['name'] ?></td>
    <td>
        <?php
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
        ?>
    </td>
    <td class="actions center">
        <?php
        if ($u['id'] != $this->request->session()->read('User.id') && $u['id'] != 1) {
            echo $this->Guardian->protectedConfirmationLink('<i class="icon-delete"></i>', '/backend/users/delete/' . $u['id'], [
                'title' => 'Diesen Benutzer löschen',
                'confirm-message' => __d('wasabi_core', 'Benutzer <strong>{0}</strong> wirklich löschen?', $u['username']),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                'escape' => false
            ]);
        } else {
            echo '-';
        }
        ?>
    </td>
</tr>