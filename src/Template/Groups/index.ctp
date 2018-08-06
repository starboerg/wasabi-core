<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Cake\ORM\Query $groups
 */

$this->Html->setTitle(__d('wasabi_core', 'Groups'));
$this->Html->setSubTitle(__d('wasabi_core', 'Management'));
$this->Html->addAction(
    $this->Guardian->protectedLink(
        $this->Icon->addPlus(),
        $this->Route->groupsAdd(),
        [
            'title' => __d('wasabi_core', 'Create a new Group'),
            'class' => 'add',
            'escape' => false
        ])
);
?>
<?= $this->Form->create($this->Filter->getContext(), [
    'id' => false,
    'class' => 'filter-form',
    'url' => $this->Route->groupsIndex(),
    'valueSources' => ['context']
]) ?>
<div class="row pagination"><?= ($pagination = $this->Filter->pagination(5, __d('wasabi_core', 'Groups'))) ?></div>
<div class="datatable-wrapper">
    <table class="datatable valign-middle">
        <thead>
        <tr class="datatable-filters">
            <th><?= $this->Form->control('id', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'ID'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <th><?= $this->Form->control('name', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search for Group'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <th><?= $this->Form->control('description', ['type' => 'text', 'id' => false, 'label' => false, 'placeholder' => __d('wasabi_core', 'Search in Description'), 'templates' => 'Wasabi/Core.FormTemplates/filter']) ?></th>
            <th></th>
            <th class="center"><?= $this->Form->button(__d('wasabi_core', 'Search'), ['class' => 'button blue', 'data-toggle' => 'btn-loading']); ?></th>
        </tr>
        <tr class="datatable-headers">
            <th class="t-1-12 center"><?= $this->Filter->sortLink('ID', 'id') ?></th>
            <th class="t-3-12"><?= $this->Filter->sortLink(__d('wasabi_core', 'Group'), 'name') ?></th>
            <th class="t-5-12"><?= __d('wasabi_core', 'Description') ?></th>
            <th class="t-2-12"><?= $this->Filter->sortLink(__d('wasabi_core', '# Users'), 'user_count') ?></th>
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
