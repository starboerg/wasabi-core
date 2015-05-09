<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $g group
 */
?><tr<?= $class ?>>
    <td class="col-id center"><?= $g['id'] ?></td>
    <td class="col-name"><?= $this->Guardian->protectedLink(
            $g['name'],
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Groups',
                'action' => 'edit',
                $g['id']
            ],
            ['title' => __d('wasabi_core', 'Edit Group "{0}"', $g['name'])],
            true
        )
        ?></td>
    <td class="col-user-count"><?= $g['user_count'] ?></td>
    <td class="col-actions center">
        <?php
        if ($g['id'] != 1) {
            echo $this->Guardian->protectedConfirmationLink(
                '<i class="wicon-remove"></i>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Groups',
                    'action' => 'delete',
                    $g['id']
                ],
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