<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var array $users
 * @var array $groups
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
);?>
<?= $this->Form->create(false, [
    'id' => false,
    'class' => 'filter-form',
    'url' => [
        'plugin' => $this->request->params['plugin'],
        'controller' => $this->request->params['controller'],
        'action' => 'index'
    ]
]) ?>
<div class="row pagination"><?= ($pagination = $this->Filter->pagination(5, __d('wasabi_core', 'Users'))) ?></div>
<table class="list users valign-middle">
    <thead>
    <tr class="filters">
        <th><?= $this->Form->input('user_id', array('type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'ID'), 'templates' => 'Wasabi/Core.form_templates_filter')) ?></th>
        <th><?= $this->Form->input('username', array('type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search for username'), 'templates' => 'Wasabi/Core.form_templates_filter')) ?></th>
        <th><?= $this->Form->input('email', array('type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search for email'), 'templates' => 'Wasabi/Core.form_templates_filter')) ?></th>
        <th><?= $this->Form->input('group_id', array('options' => $groups, 'label' => false, 'id' => false, 'empty' => __d('wasabi_core', 'Filter by group'), 'templates' => 'Wasabi/Core.form_templates_filter')) ?></th>
        <th></th>
        <th><?= $this->Form->button(__d('wasabi_core', 'Search'), array('class' => 'button blue', 'type' => 'submit')); ?></th>
    </tr>
    <tr>
        <th class="t-1-12 center">ID</th>
        <th class="t-2-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'User'), 'user') ?></th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Email'), 'email') ?></th>
        <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Group'), 'group') ?></th>
        <th class="t-2-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Status'), 'status') ?></th>
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
<div class="row pagination"><?= $pagination ?></div>
<?= $this->Form->end() ?>