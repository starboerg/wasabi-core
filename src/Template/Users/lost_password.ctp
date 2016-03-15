<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'lost-password']);
?>
<?= $this->element('Wasabi/Core.support-image') ?>
<?= $this->Form->create($user, ['novalidate' => 'novalidate']) ?>
    <div class="support-content">
        <h1><?= __d('wasabi_core', 'Lost Password') ?></h1>
        <?= $this->Flash->render() ?>
        <p><?= __d('wasabi_core', 'If you forgot your password, enter your email address below. After submitting this form you will receive an email with further instruction on how to reset your password.') ?></p>
        <?= $this->Form->input('email', ['label' => __d('wasabi_core', 'Email') . ':']) ?>
    </div>
    <div class="form-controls">
        <ul>
            <li><?= $this->Html->link(__d('wasabi_core', 'Remember your password?'), [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'login'
            ]) ?></li>
        </ul>
        <?= $this->Form->button(__d('wasabi_core', 'Send'), ['class' => 'button blue']) ?>
    </div>
<?= $this->Form->end() ?>
