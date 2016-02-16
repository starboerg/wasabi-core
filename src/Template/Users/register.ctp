<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'register']);
?>
<div class="support-image">
    <?= $this->Html->image('/wasabi/core/img/wasabi.png') ?>
</div>
<?= $this->Form->create($user, ['novalidate' => 'novalidate']) ?>
    <div class="support-content">
        <h1><?= __d('wasabi_core', 'Register') ?></h1>
        <?= $this->Flash->render() ?>
        <?= $this->Form->input('username', ['label' => __d('wasabi_core', 'Username') . ':']) ?>
        <?= $this->Form->input('email', ['label' => __d('wasabi_core', 'Email') . ':']) ?>
        <?= $this->Form->input('password', ['label' => __d('wasabi_core', 'Password') . ':']) ?>
        <?= $this->Form->input('password_confirmation', ['label' => __d('wasabi_core', 'Password Confirmation') . ':']) ?>
    </div>
    <div class="form-controls">
        <ul>
            <li><?= $this->Html->link(__d('wasabi_core', 'Or log in.'), [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'login'
            ]) ?></li>
        </ul>
        <?= $this->Form->button(__d('wasabi_core', 'Submit'), ['class' => 'button blue']) ?>
    </div>
<?= $this->Form->end() ?>
