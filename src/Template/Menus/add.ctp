<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Menu $menu
 */

if ($this->request->params['action'] === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new Menu'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit Menu'));
    $this->Html->setSubTitle($menu->get('name'));
}

$isEdit = ($this->request->params['action'] === 'edit');

$nameOpts = ['label' => __d('wasabi_core', 'Menu Name')];

if (!$isEdit) {
    $nameOpts['class'] = 'get-focus';
}

echo $this->Form->create($menu, array('class' => 'no-top-section'));
    if ($isEdit) {
        echo $this->Form->input('id', array('type' => 'hidden'));
    }
    echo $this->Form->input('name', $nameOpts);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), array('div' => false, 'class' => 'button'));
        echo $this->Html->backendLink(__d('wasabi_core', 'Cancel'), ['plugin' => 'Wasabi/Core', 'controller' => 'Menus', 'action' => 'index']);
    echo $this->Html->tag('/div');
echo $this->Form->end();
