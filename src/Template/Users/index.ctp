<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $users
 */

$this->Html->setTitle(__d('wasabi_core', 'User'));
$this->Html->setSubTitle(__d('wasabi_core', 'Management'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        '<i class="icon-plus"></i>',
        [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'add'
        ],
        [
            'title' => __d('wasabi_core', 'Create a new User'),
            'class' => 'add',
            'escape' => false
        ])
);
?>
<table class="list users valign-middle">
    <thead>
    <tr>
        <th class="t-1-12 center">ID</th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'User'), 'user') ?></th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Email'), 'email') ?></th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Group'), 'group') ?></th>
        <th class="t-1-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Status'), 'status') ?></th>
        <th class="t-1-12 center"><?= __d('wasabi_core', 'Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($users as $key => $u) {
        echo $this->element('../Users/__user-row', [
            'class' => (($key + 1) % 2 == 0) ? ' class="even"' : '',
            'u' => $u
        ]);
    }
    ?>
    </tbody>
</table>