<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var integer $level
 * @var integer $key
 * @var array $menuItem
 */
use Wasabi\Core\Model\Table\MenuItemsTable;

?>
<div class="row">
    <div class="grid-10-16">
        <div class="row">
            <div class="spacer">&nbsp;</div>
            <?= $this->Guardian->protectedLink($menuItem['name'], [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Menus',
                'action' => 'editItem',
                $menuItem['id']
            ]); ?>
        </div>
    </div>
    <div class="grid-2-16 center">active</div>
    <div class="grid-2-16 center">
        <a href="javascript:void(0)" class="wicon-move move"
           title="<?= __d('wasabi_core', 'Change the position of this Menu Item') ?>">move</a>
    </div>
    <div class="grid-2-16 center actions">
        <?php
        $options = [
            'class' => 'wicon-add',
            'title' => __d('wasabi_core', 'Add a child to this Menu Item')
        ];
        if ($level > MenuItemsTable::MAXIMUM_NESTING_LEVEL) {
            $options['class'] .= ' hidden';
        }
        echo $this->Guardian->protectedLink(__d('wasabi_core', 'add parent'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Menus',
            'action' => 'addItem',
            $menuItem['menu_id'],
            $menuItem['id'],
        ], $options);
        echo $this->Guardian->protectedConfirmationLink(
            '<i class="wicon-remove"></i>',
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Menus',
                'action' => 'deleteItem',
                $menuItem['id']
            ], [
                'title' => __d('wasabi_core', 'Delete this Menu Item'),
                'confirm-message' => __d('wasabi_core', 'Do you really want to delete the menu item <strong>{0}</strong>?', $menuItem['name']),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                'escape' => false
            ]
        );
        ?>
    </div>
</div>