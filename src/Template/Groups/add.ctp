<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Group $group
 */

if ($this->request->getParam('action') === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new Group'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit Group'));
    $this->Html->setSubTitle($group->get('name'));
}

$isEdit = ($this->request->getParam('action') === 'edit');

echo $this->Form->create($group, ['class' => 'no-top-section']);
    if ($isEdit) {
        echo $this->Form->control('id', ['type' => 'hidden']);
    }
    echo $this->Form->control('name', [
        'label' => __d('wasabi_core', 'Group Name')
    ]);
    echo $this->Form->control('description', [
        'label' => __d('wasabi_core', 'Description')
    ]);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
        echo $this->Guardian->protectedLink(
            __d('wasabi_core', 'Cancel'),
            $this->Filter->getBacklink($this->Route->groupsIndex())
        );
    echo $this->Html->tag('/div');
echo $this->Form->end();
