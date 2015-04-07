<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $groups
 */

$this->Html->setTitle(__d('wasabi_core', 'Groups'));
$this->Html->setSubTitle(__d('wasabi_core', 'Management'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        '<i class="icon-plus"></i>',
        [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'add'
        ],
        [
            'title' => __d('wasabi_core', 'Create a new Group'),
            'class' => 'add',
            'escape' => false
        ])
);
?>
<table class="list groups valign-middle">
    <thead>
    <tr>
        <th class="t-1-12 center">ID</th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Group'), 'group') ?></th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', '# Users'), 'count') ?></th>
        <th class="t-1-12 center"><?= __d('wasabi_core', 'Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($groups as $key => $g) {
        echo $this->element('../Groups/__group-row', [
            'class' => (($key + 1) % 2 == 0) ? ' class="even"' : '',
            'g' => $g
        ]);
    }
    ?>
    </tbody>
</table>