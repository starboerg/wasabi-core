<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['wasabi-core--layout-support']);
?>
<?= $this->element('Wasabi/Core.support-image') ?>
<?= $this->Form->create($user, ['novalidate' => 'novalidate']) ?>
<div class="support-content">
    <h1><?= __d('wasabi_core', 'Request Verification Email') ?></h1>
    <?= $this->Flash->render() ?>
    <p><?= __d('wasabi_core', 'If you did not verify your email address yet, you can request a new verification email below. After submitting this form you will receive an email with further instruction on how to verify your email address.') ?></p>
    <?= $this->Form->control('email', ['label' => __d('wasabi_core', 'Email') . ':']) ?>
</div>
<div class="form-controls">
    <div class="form-control-buttons">
        <?= $this->Form->button(__d('wasabi_core', 'Send'), ['class' => 'button blue', 'data-toggle' => 'btn-loading']) ?>
    </div>
</div>
<?= $this->Form->end() ?>
