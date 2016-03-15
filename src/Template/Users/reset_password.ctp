<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'reset-password']);
?>
<?= $this->element('Wasabi/Core.support-image') ?>
<?= $this->Form->create($user, ['novalidate' => 'novalidate']); ?>
    <div class="support-content">
        <h1><?= __d('wasabi_core', 'Reset Password') ?></h1>
        <?= $this->Flash->render() ?>
        <p><?= __d('wasabi_core', 'Please enter your new password.') ?> <?= __d('wasabi_core', 'The password should consist of 6 to 50 characters. All numbers, letters and special characters are allowed.') ?></p>
        <?= $this->Form->input('password', [
            'label' => __d('wasabi_core', 'Password'),
            'autocomplete' => 'off'
        ]); ?>
        <?= $this->Form->input('password_confirmation', [
            'label' => __d('wasabi_core', 'Password Confirmation'),
            'type' => 'password',
            'autocomplete' => 'off'
        ]); ?>
    </div>
    <div class="form-controls">
        <?= $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button blue']) ?>
    </div>
<?= $this->Form->end() ?>
