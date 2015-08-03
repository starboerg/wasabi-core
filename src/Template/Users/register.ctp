<?php
/**
 * @var \Wasabi\Core\Model\Entity\User $user
 */
?>
<div class="users form">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __d('wasabi_core', 'Add User') ?></legend>
        <?= $this->Form->input('username') ?>
        <?= $this->Form->input('password') ?>
        <?= $this->Form->input('email') ?>
    </fieldset>
    <?= $this->Form->button(__d('wasabi_core', 'Submit')); ?>
    <?= $this->Form->end() ?>
</div>