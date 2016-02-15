<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'verify-by-token']);
?>
<div class="support-image">
    <?= $this->Html->image('/wasabi/core/img/wasabi.png') ?>
</div>
<form>
<div class="support-content">
    <h1><?= __d('wasabi_core', 'Email Verified') ?></h1>
    <p><?= __d('wasabi_core', 'You successfully verified your email address!') ?></p>
    <p><?= __d('wasabi_core', 'Your account will be activated by an administrator soon. You will be notified about the activation via email.') ?></p>
</div>
    <div class="form-controls row">
        <ul>
            <li><?= $this->Html->link(__d('wasabi_core', 'Go to homepage'), '/') ?></li>
        </ul>
    </div>
</form>
