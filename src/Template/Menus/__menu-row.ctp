<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $m menu
 */
?>
<tr<?= $class ?>>
    <td class="col-id center"><?= $m['id'] ?></td>
    <td class="col-name"><?= $this->Guardian->protectedLink(
            $m['name'],
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Menus',
                'action' => 'edit',
                $m['id']
            ],
            ['title' => __d('wasabi_core', 'Edit Menu "{0}"', $m['name'])],
            true
        )
        ?></td>
    <td class="col-menu-item-count"><?= $m['menu_item_count'] ?></td>
    <td class="col-actions center">
        <?php
        echo $this->Guardian->protectedConfirmationLink(
            '<i class="wicon-remove"></i>',
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Menus',
                'action' => 'delete',
                $m['id']
            ],
            [
                'escapeTitle' => false,
                'class' => 'action-delete',
                'title' => __d('wasabi_core', 'Delete Menu'),
                'confirm-message' => __d('wasabi_core', 'Do you really want to delete menu <strong>{0}</strong>?', $m['name']),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion')
            ]
        );
        ?>
    </td>
</tr>