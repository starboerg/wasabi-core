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

$nameOpts = ['label' => __d('wasabi_core', 'Group Name')];

if (!$isEdit) {
    $nameOpts['class'] = 'get-focus';
}

echo $this->Form->create($group, array('class' => 'no-top-section'));
    if ($isEdit) {
        echo $this->Form->input('id', array('type' => 'hidden'));
    }
    echo $this->Form->input('name', $nameOpts);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), array('div' => false, 'class' => 'button'));
        echo $this->Html->backendLink(__d('wasabi_core', 'Cancel'), '/backend/groups');
    echo $this->Html->tag('/div');
echo $this->Form->end();
