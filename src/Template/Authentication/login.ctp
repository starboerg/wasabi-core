<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;

$this->set('bodyCssClass', ['wasabi-core--layout-support']);

$message = Configure::read('Settings.Core.Login.Message.text');

if (Configure::read('Settings.Core.Login.Message.show') === '1' && $message) {
    $msgBoxClasses = ['msg-box'];
    $class = Configure::read('Settings.Core.Login.Message.class');
    $msgBoxClasses[] = $class ? $class : 'info';
    echo $this->Html->tag('div', $message, ['class' => join(' ', $msgBoxClasses)]);
}
?>
<?= $this->element('Wasabi/Core.support-image') ?>
<?= $this->Form->create(null, ['novalidate' => 'novalidate']) ?>
<div class="support-content">
    <h1><?= __d('wasabi_core', 'Login') ?></h1>
    <?= $this->Flash->render() ?>
    <?= $this->Flash->render('auth') ?>
    <?= $this->element('Wasabi/Core.login-form-inputs') ?>
</div>
<div class="form-controls">
    <ul>
        <?php if (Configure::read('Core.User.can_register')) { ?>
        <li>
            <?= $this->Html->link(
                __d('wasabi_core', 'Create an account.'),
                $this->Route->register()
            ) ?>
        </li>
        <?php } ?>
    </ul>
    <div class="form-control-buttons">
        <?= $this->Form->button(__d('wasabi_core', 'Login'), ['class' => 'button blue', 'data-toggle' => 'btn-loading']) ?>
    </div>
</div>
<?= $this->Form->end() ?>
<div class="bottom-links">
    <ul>
        <li>
            <?= $this->Html->link(
                __d('wasabi_core', 'Lost your Password?'),
                $this->Route->lostPassword()
            ) ?>
        </li>
    </ul>
</div>
