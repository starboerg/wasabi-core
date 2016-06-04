<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Group $group
 */

if ($this->request->params['action'] === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new Group'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit Group'));
    $this->Html->setSubTitle($group->get('name'));
}

$isEdit = ($this->request->params['action'] === 'edit');

echo $this->Form->create($group, ['class' => 'no-top-section']);
    if ($isEdit) {
        echo $this->Form->input('id', ['type' => 'hidden']);
    }
    echo $this->Form->input('name', [
        'label' => __d('wasabi_core', 'Group Name')
    ]);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), ['div' => false, 'class' => 'button']);
        echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Groups',
            'action' => 'index'
        ]);
    echo $this->Html->tag('/div');
echo $this->Form->end();
