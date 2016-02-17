<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Group $group
 */
?><tr>
    <td class="col-id center"><?= $group->id ?></td>
    <td class="col-name"><?= $this->Guardian->protectedLink(
            $group->name,
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Groups',
                'action' => 'edit',
                $group->id
            ],
            ['title' => __d('wasabi_core', 'Edit Group "{0}"', $group->name)],
            true
        )
        ?></td>
    <td class="col-user-count"><?= $group->user_count ?></td>
    <td class="col-actions center">
        <?php
        if ($group->id != 1) {
            echo $this->Guardian->protectedConfirmationLink(
                '<i class="wicon-remove"></i>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Groups',
                    'action' => 'delete',
                    $group->id
                ],
                [
                    'escapeTitle' => false,
                    'class' => 'action-delete',
                    'title' => __d('wasabi_core', 'Delete Group'),
                    'confirm-message' => __d('wasabi_core', 'Do you really want to delete group <strong>{0}</strong>?', $group->name),
                    'confirm-title' => __d('wasabi_core', 'Confirm Deletion')
                ]
            );
        } else {
            echo '-';
        }
        ?>
    </td>
</tr>
