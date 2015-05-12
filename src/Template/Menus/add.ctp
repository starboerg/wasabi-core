<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Menu $menu
 * @var \Wasabi\Core\Model\Entity\MenuItem $menuItems
 */

$isEdit = false;
if ($this->request->params['action'] === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new Menu'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit Menu'));
    $this->Html->setSubTitle($menu->get('name'));
    $isEdit = true;
}

$nameOpts = ['label' => __d('wasabi_core', 'Menu Name')];

if (!$isEdit) {
    $nameOpts['class'] = 'get-focus';
}

echo $this->Form->create($menu, array('class' => 'no-top-section'));

if ($isEdit) {
    echo $this->Form->input('id', array('type' => 'hidden'));
}
echo $this->Form->input('name', $nameOpts); ?>
<div class="form-row row">
    <label><?= __d('wasabi_core', 'Menu Items') ?></label>
    <div class="field<?= (!$isEdit) ? ' no-input' : '' ?>">
        <?php if($isEdit): ?>
            <div class="msg-box info"><?= __d('wasabi_core', 'Tip: The maximum nesting level is <strong>2</strong>.') ?></div>
            <div class="list-header row">
				<div class="span10"><?= __d('wasabi_core', 'Menu Item') ?></div>
				<div class="span2 center"><?= __d('wasabi_core', 'Status') ?></div>
				<div class="span2 center"><?= __d('wasabi_core', 'Sort') ?></div>
				<div class="span2 center"><?= __d('wasabi_core', 'Actions') ?></div>
			</div>
            <ul id="menu-items" class="list-content" data-reorder-url="<?= $this->Html->getBackendUrl('/menus/reorder_items', true) ?>">
                <?php if($menuItems = $menuItems->toArray()): ?>
                    <?= $this->Menu->renderTree($menuItems) ?>
                <?php else: ?>
                    <li class="no-items center"><?= __d('wasabi_core', 'This Menu has no items yet.') ?></li>
                <?php endif; ?>
            </ul>
        <div class="bottom-links">
            <?= $this->Html->backendLink(__d('wasabi_core', 'Add a new Menu Item'), [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Menus',
                'action' => 'add_item',
                $menu['id']
            ]) ?>
        </div>
        <?php else: ?>
            <?= __d('wasabi_core', 'You can start adding Menu Items after you created the Menu.') ?>
        <?php endif; ?>
    </div>
</div>
<div class="form-controls">
    <?php
    echo $this->Form->button(__d('wasabi_core', 'Save'), ['div' => false, 'class' => 'button']);
    echo $this->Html->backendLink(__d('wasabi_core', 'Cancel'), [
        'plugin' => 'Wasabi/Core',
        'controller' => 'Menus',
        'action' => 'index']
    );
    ?>
</div>
<?= $this->Form->end() ?>
