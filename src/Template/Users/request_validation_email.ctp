<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'request-validation-email']);
?>
<div class="support-image">
    <?= $this->Html->image('/wasabi/core/img/wasabi.png') ?>
</div>
<?= $this->Form->create($user, ['novalidate' => 'novalidate']) ?>
<div class="support-content">
    <h1><?= __d('wasabi_core', 'Request Validation Email') ?></h1>
    <?= $this->Flash->render() ?>
    <p><?= __d('wasabi_core', 'If you did not yet verify your email address, request a new validation email below.') ?></p>
    <?= $this->Form->input('email', ['label' => __d('wasabi_core', 'Email') . ':']) ?>
</div>
<div class="form-controls">
    <ul>
        <li><?= $this->Html->link(__d('wasabi_core', 'Back to Login'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'login'
        ]) ?></li>
    </ul>
    <?= $this->Form->button(__d('wasabi_core', 'Submit'), ['class' => 'button blue']) ?>
</div>
<?= $this->Form->end() ?>
