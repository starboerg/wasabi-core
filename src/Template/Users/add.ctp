<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var \Cake\Collection\Collection $groups
 */

if ($this->request->params['action'] === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new User'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit User'));
    $this->Html->setSubTitle($user->get('username'));
}

$isEdit = ($this->request->params['action'] === 'edit');

if (!$isEdit) {
    $usernameOpts['class'] = 'get-focus';
}

echo $this->Form->create($user, array('class' => 'no-top-section'));
    if ($isEdit) {
        echo $this->Form->input('id', array('type' => 'hidden'));
    }
    echo $this->Form->input('username', [
        'label' => __d('wasabi_core', 'Username')
    ]);
    echo $this->Form->input('email', [
        'label' => __d('wasabi_core', 'Email')
    ]);
    echo $this->Form->input('group_id', [
        'label' => __d('wasabi_core', 'Group'),
        'options' => $groups,
        'empty' => __d('wasabi_core', 'Please choose...')
    ]);
    if ($isEdit) {
        echo $this->Form->widget('section', [
            'title' => __d('wasabi_core', 'Change Password'),
            'description' => __d('wasabi_core', 'To change the user\'s password fill in both password fields. Otherwise leave those fields empty.')
        ]);
    }
    echo $this->Form->input('password', [
        'label' => __d('wasabi_core', 'Password'),
        'info' => __d('wasabi_core', 'Should consist of 6 to 50 characters. All numbers, letters and special characters are allowed.'),
        'autocomplete' => 'off'
    ]);
    echo $this->Form->input('password_confirmation', [
        'label' => __d('wasabi_core', 'Password Confirmation'),
        'type' => 'password',
        'autocomplete' => 'off'
    ]);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), array('div' => false, 'class' => 'button'));
        echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), $this->Filter->getBacklink([
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'index'
        ]));
    echo $this->Html->tag('/div');
echo $this->Form->end();
