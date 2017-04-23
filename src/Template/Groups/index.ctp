<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Cake\ORM\Query $groups
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
<?= $this->Form->create(false, [
    'id' => false,
    'class' => 'filter-form',
    'url' => [
        'plugin' => $this->request->params['plugin'],
        'controller' => $this->request->params['controller'],
        'action' => 'index'
    ]
]) ?>
<div class="row pagination"><?= ($pagination = $this->Filter->pagination(5, __d('wasabi_core', 'Groups'))) ?></div>
<div class="datatable-wrapper">
    <table class="datatable valign-middle">
        <thead>
        <tr class="datatable-filters">
            <th><?= $this->Form->input('id', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'ID'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <th><?= $this->Form->input('group', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search for Group'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <th><?= $this->Form->input('description', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search in Description'), 'templates' => 'Wasabi/Core.form_templates_filter']) ?></th>
            <th></th>
            <th class="center"><?= $this->Form->button(__d('wasabi_core', 'Search'), ['class' => 'button blue', 'type' => 'submit']); ?></th>
        </tr>
        <tr class="datatable-headers">
            <th class="t-1-12 center"><?= $this->Filter->sortLink('ID', 'id') ?></th>
            <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Group'), 'group') ?></th>
            <th class="t-5-12"><?= __d('wasabi_core', 'Description') ?></th>
            <th class="t-2-12"><?= $this->Filter->sortLink(__d('wasabi_core', '# Users'), 'count') ?></th>
            <th class="t-1-12 center"><?= __d('wasabi_core', 'Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($groups as $group) {
            echo $this->element('../Groups/__group-row', [
                'group' => $group
            ]);
        }
        if ($groups->all()->count() === 0) { ?>
            <td class="no-results" colspan="5"><?= __d('wasabi_core', 'Your search yields no results. Please adjust your search criteria.') ?></td>
        <?php } ?>
        </tbody>
    </table>
</div>
<div class="row pagination"><?= $pagination ?></div>
<?= $this->Form->end() ?>
