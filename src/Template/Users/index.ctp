<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Cake\ORM\Query $users
 * @var \Cake\ORM\Query $groups
 * @var array $statusOptions
 * @var boolean $displayUsername
 * @var boolean $displayFirstnameLastname
 */

$this->Html->setTitle(__d('wasabi_core', 'User'));
$this->Html->setSubTitle(__d('wasabi_core', 'Management'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        $this->Icon->addPlus(),
        $this->Route->usersAdd(),
        [
            'title' => __d('wasabi_core', 'Create a new User'),
            'class' => 'add',
            'escape' => false
        ])
);?>
<?= $this->Form->create($this->Filter->getContext(), [
    'id' => false,
    'class' => 'filter-form',
    'url' => $this->Route->usersIndex()
]) ?>
<div class="row pagination"><?= ($pagination = $this->Filter->pagination(5, __d('wasabi_core', 'Users'))) ?></div>
<div class="datatable-wrapper">
    <table class="datatable valign-middle">
        <thead>
        <tr class="datatable-filters">
            <th><?= $this->Form->control('user_id', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'ID'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <?php if ($displayUsername): ?>
            <th><?= $this->Form->control('username', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search by username'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <?php endif; ?>
            <?php if ($displayFirstnameLastname): ?>
                <th><?= $this->Form->control('name', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search by name'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <?php endif; ?>
            <th><?= $this->Form->control('email', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search for email'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <th><?= $this->Form->control('group_id', ['options' => $groups, 'label' => false, 'id' => false, 'empty' => __d('wasabi_core', 'Filter by group'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <th><?= $this->Form->control('status', ['options' => $statusOptions, 'label' => false, 'id' => false, 'empty' => __d('wasabi_core', 'Filter by status'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <th class="center"><?= $this->Form->button(__d('wasabi_core', 'Search'), ['class' => 'button blue', 'data-toggle' => 'btn-loading']); ?></th>
        </tr>
        <tr>
            <th class="t-1-12 center"><?= $this->Filter->sortLink('ID', 'id') ?></th>
            <?php if ($displayUsername): ?>
            <th><?= $this->Filter->sortLink(__d('wasabi_core', 'Username'), 'username') ?></th>
            <?php endif; ?>
            <?php if ($displayFirstnameLastname): ?>
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
                'user' => $user,
                'displayUsername' => $displayUsername,
                'displayFirstnameLastname' => $displayFirstnameLastname
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
