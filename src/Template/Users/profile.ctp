<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->Html->setTitle(__d('wasabi_core', 'Edit Profle'));
$this->Html->setSubTitle($user->get('username'));

echo $this->Form->create($user, ['class' => 'no-top-section']);
echo $this->Form->input('id', ['type' => 'hidden']);
echo $this->Form->input('email', [
    'label' => __d('wasabi_core', 'Email')
]);
echo $this->Form->widget('section', [
    'title' => __d('wasabi_core', 'Change Password'),
    'description' => __d('wasabi_core', 'To change your password fill in both password fields. Otherwise leave those fields empty.')
]);
echo $this->Form->input('password', [
    'label' => __d('wasabi_core', 'Password'),
    'info' => __d('wasabi_core', 'The password should consist of 6 to 50 characters. All numbers, letters and special characters are allowed.'),
    'autocomplete' => 'off',
    'value' => ''
]);
echo $this->Form->input('password_confirmation', [
    'label' => __d('wasabi_core', 'Password Confirmation'),
    'type' => 'password',
    'autocomplete' => 'off'
]);
echo $this->Form->widget('section', [
    'title' => __d('wasabi_core', 'Language and Time'),
    'description' => __d('wasabi_core', 'Adjust these settings for your current location.')
]);
echo $this->Form->input('language_id', [
    'label' => __d('wasabi_core', 'Backend Interface Language'),
    'options' => $languages,
    'empty' => __d('wasabi_core', 'Please choose...')
]);
echo $this->Form->timeZoneSelect('timezone');
echo $this->Html->div('form-controls');
echo $this->Form->button(__d('wasabi_core', 'Save'), ['div' => false, 'class' => 'button']);
echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), $this->Filter->getBacklink([
    'plugin' => 'Wasabi/Core',
    'controller' => 'Users',
    'action' => 'index'
]));
echo $this->Html->tag('/div');
echo $this->Form->end();
