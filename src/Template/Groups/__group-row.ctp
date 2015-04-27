<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $g group
 */
?><tr<?= $class ?>>
    <td class="center"><?= $g['id'] ?></td>
    <td><?= $this->Guardian->protectedLink(
                $g['name'],
                '/backend/groups/edit/' . $g['id'],
                ['title' => __d('wasabi_core', 'Edit Group "{0}"', $g['name'])],
                true
            )
    ?></td>
    <td><?= $g['user_count'] ?></td>
    <td class="actions center">
        <?php
        if ($g['id'] != 1) {
            echo $this->Guardian->protectedConfirmationLink(
                '<i class="icon-delete"></i>',
                '/backend/groups/delete/' . $g['id'],
                [
                    'escapeTitle' => false,
                    'class' => 'action-delete',
                    'title' => __d('wasabi_core', 'Delete Group'),
                    'confirm-message' => __d('wasabi_core', 'Do you really want to delete group <strong>{0}</strong>?', $g['name']),
                    'confirm-title' => __d('wasabi_core', 'Confirm Deletion')
                ]
            );
        } else {
            echo '-';
        }
        ?>
    </td>
</tr>