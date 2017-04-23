<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Cake\ORM\Query $users
 * @var \Cake\ORM\Query $groups
 */
use Wasabi\Core\Wasabi;

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
<div class="datatable-wrapper">
    <table class="datatable valign-middle">
        <thead>
        <tr class="datatable-filters">
            <th><?= $this->Form->input('user_id', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'ID'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <?php if (Wasabi::setting('Core.User.has_username')): ?>
            <th><?= $this->Form->input('username', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search by username'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <?php endif; ?>
            <?php if (Wasabi::setting('Core.User.has_firstname_lastname')): ?>
                <th><?= $this->Form->input('name', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search by name'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <?php endif; ?>
            <th><?= $this->Form->input('email', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search for email'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <th><?= $this->Form->input('group_id', ['options' => $groups, 'label' => false, 'id' => false, 'empty' => __d('wasabi_core', 'Filter by group'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <th></th>
            <th class="center"><?= $this->Form->button(__d('wasabi_core', 'Search'), ['class' => 'button blue', 'type' => 'submit']); ?></th>
        </tr>
        <tr>
            <th class="t-1-12 center"><?= $this->Filter->sortLink('ID', 'id') ?></th>
            <?php if (Wasabi::setting('Core.User.has_username')): ?>
            <th><?= $this->Filter->sortLink(__d('wasabi_core', 'Username'), 'username') ?></th>
            <?php endif; ?>
            <?php if (Wasabi::setting('Core.User.has_firstname_lastname')): ?>
                <th><?= $this->Filter->sortLink(__d('wasabi_core', 'Name'), 'name') ?></th>
            <?php endif; ?>
            <th class="t-2-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Email'), 'email') ?></th>
            <th class="t-2-12"><?= __d('wasabi_core', 'Groups') ?></th>
            <th class="t-2-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Status'), 'status') ?></th>
            <th class="t-1-12 center"><?= __d('wasabi_core', 'Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($users as $user) {
            echo $this->element('../Users/__user-row', [
                'user' => $user
            ]);
        }
        if ($users->all()->count() === 0) { ?>
            <td class="no-results" colspan="6"><?= __d('wasabi_core', 'Your search yields no results. Please adjust your search criteria.') ?></td>
        <?php } ?>
        </tbody>
    </table>
</div>
<div class="row pagination"><?= $pagination ?></div>
<?= $this->Form->end() ?>
