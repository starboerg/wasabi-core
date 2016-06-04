<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Group $group
 * @var \Cake\ORM\Query $groups
 */

$this->Html->setTitle(__d('wasabi_core', 'Move existing Member(s)'));
$this->Html->setSubTitle($group->name);

echo $this->Form->create($group, ['class' => 'no-top-section', 'type' => 'post']);
    echo $this->Form->input('alternative_group_id', [
        'label' => __d('wasabi_core', 'Alternative Group'),
        'options' => $groups,
        'templateVars' => [
            'info' => __d('wasabi_core', 'Please select a group where the existing member(s) should be moved to.')
        ]
    ]);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Move Members & Delete Group'), ['div' => false, 'class' => 'button red']);
        echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'index',
        ]);
    echo $this->Html->tag('/div');
echo $this->Form->end();
