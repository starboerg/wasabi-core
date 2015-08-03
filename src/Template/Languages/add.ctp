<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Language $language
 */

if ($this->request->params['action'] === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new Language'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit Language'));
    $this->Html->setSubTitle($language->get('name'));
}

$isEdit = ($this->request->params['action'] === 'edit');

$nameOpts = ['label' => __d('wasabi_core', 'Language Name')];

if (!$isEdit) {
    $nameOpts['class'] = 'get-focus';
}

echo $this->Form->create($language, ['class' => 'no-top-section']);
if ($isEdit) {
    echo $this->Form->input('id', ['type' => 'hidden']);
}
echo $this->Form->input('name', $nameOpts);
echo $this->Form->input('iso2', ['label' => __d('wasabi_core', 'ISO 639-1')]);
echo $this->Form->input('iso3', ['label' => __d('wasabi_core', 'ISO 639-2/T')]);
echo $this->Form->input('lang', ['label' => __d('wasabi_core', 'HTML lang')]);
echo $this->Form->input('available_at_frontend', ['label' => __d('wasabi_core', 'available at Frontend'), 'type' => 'checkbox', 'formRowLabel' => 'Frontend']);
echo $this->Form->input('available_at_backend', ['label' => __d('wasabi_core', 'available at Backend'), 'type' => 'checkbox', 'formRowLabel' => 'Backend']);
echo $this->Html->div('form-controls');
    echo $this->Form->button(__d('wasabi_core', 'Save'), ['div' => false, 'class' => 'button']);
    echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), [
        'plugin' => 'Wasabi/Core',
        'controller' => 'Languages',
        'action' => 'index'
    ]);
echo $this->Html->tag('/div');
echo $this->Form->end();
