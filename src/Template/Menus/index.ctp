<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $menus
 */

$this->Html->setTitle(__d('wasabi_core', 'Menus'));
$this->Html->setSubTitle(__d('wasabi_core', 'Management'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        '<i class="icon-plus"></i>',
        [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Menus',
            'action' => 'add'
        ],
        [
            'title' => __d('wasabi_core', 'Create a new Menu'),
            'class' => 'add',
            'escape' => false
        ])
); ?>
<table class="list menus valign-middle">
    <thead>
    <tr>
        <th class="t-3-16 center">ID</th>
        <th class="t-6-16"><?= $this->Filter->sortLink(__d('wasabi_core', 'Menu Name'), 'name') ?></th>
        <th class="t-6-16"><?= $this->Filter->sortLink(__d('wasabi_core', '# Menu Items'), 'menu_item_count') ?></th>
        <th class="t-3-16 center"><?= __d('wasabi_core', 'Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($menus as $key => $m) {
        echo $this->element('../Menus/__menu-row', [
            'class' => (($key + 1) % 2 == 0) ? ' class="even"' : '',
            'm' => $m
        ]);
    }
    ?>
    </tbody>
</table>