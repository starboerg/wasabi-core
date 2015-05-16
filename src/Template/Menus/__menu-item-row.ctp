<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var integer $level
 * @var integer $key
 * @var array $menuItem
 */
?>
<div class="row">
    <div class="grid-10-16">
        <div class="row">
            <div class="spacer">&nbsp;</div>
            <?= $this->Guardian->protectedLink($menuItem['name'], [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Menus',
                'action' => 'edit_item',
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
        if ($level > 2) {
            $options['class'] .= ' hide';
        }
        echo $this->Guardian->protectedLink(__d('wasabi_core', 'add parent'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Menus',
            'action' => 'add_item',
            $menuItem['menu_id'],
            $menuItem['id'],
        ], $options);
        echo $this->Html->link(__d('wasabi_core', 'delete'), 'javascript:void(0)', [
            'title' => __d('wasabi_core', 'Delete this Menu Item'),
            'class' => 'wicon-remove remove-item'
        ]);
        ?>
    </div>
</div>