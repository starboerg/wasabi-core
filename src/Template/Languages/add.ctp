<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\Language $language
 */

if ($this->request->getParam('action') === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new Language'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit Language'));
    $this->Html->setSubTitle($language->name);
}

$isEdit = ($this->request->getParam('action') === 'edit');

echo $this->Form->create($language, ['class' => 'no-top-section']);
if ($isEdit) {
    echo $this->Form->control('id', ['type' => 'hidden']);
}
echo $this->Form->control('name', [
    'label' => __d('wasabi_core', 'Language Name')
]);
echo $this->Form->control('iso2', [
    'label' => __d('wasabi_core', 'ISO 639-1')
]);
echo $this->Form->control('iso3', [
    'label' => __d('wasabi_core', 'ISO 639-2/T')
]);
echo $this->Form->control('lang', [
    'label' => __d('wasabi_core', 'HTML lang')
]);
echo $this->Form->control('available_at_frontend', [
    'label' => __d('wasabi_core', 'available at Frontend'),
    'type' => 'checkbox',
    'templateVars' => [
        'formRowLabel' => 'Frontend'
    ]
]);
echo $this->Form->control('available_at_backend', [
    'label' => __d('wasabi_core', 'available at Backend'),
    'type' => 'checkbox',
    'templateVars' => [
        'formRowLabel' => 'Backend'
    ]
]);
echo $this->Html->div('form-controls');
    echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
    echo $this->Guardian->protectedLink(
        __d('wasabi_core', 'Cancel'),
        $this->Route->languagesIndex()
    );
echo $this->Html->tag('/div');
echo $this->Form->end();
