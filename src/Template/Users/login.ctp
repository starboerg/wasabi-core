<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Cake\Core\Configure;

$this->set('bodyCssClass', ['support', 'login']);

$message = Configure::read('Settings.Core.Login.Message.text');

if (Configure::read('Settings.Core.Login.Message.show') === '1' && $message) {
    $msgBoxClasses = ['msg-box'];
    $class = Configure::read('Settings.Core.Login.Message.class');
    $msgBoxClasses[] = $class ? $class : 'info';
    echo $this->Html->tag('div', $message, ['class' => join(' ', $msgBoxClasses)]);
}
?>
<?= $this->Html->image('/wasabi/core/img/wasabi.png') ?>
<?= $this->Form->create(null) ?>
    <?= $this->Flash->render('auth') ?>
    <div class="support-content">
        <?= $this->Form->input('username', ['label' => __d('wasabi_core', 'Username') . ':']) ?>
        <?= $this->Form->input('password', ['label' => __d('wasabi_core', 'Password') . ':']) ?>
        <?= $this->Form->input('remember', [
            'label' => __d('wasabi_core', 'Remember me for 2 weeks'),
            'type' => "checkbox"
        ]) ?>
    </div>
    <div class="form-controls">
        <?= $this->Form->button(__d('wasabi_core', 'Login'), ['class' => 'button blue']) ?>
    </div>
<?= $this->Form->end() ?>
<div class="bottom-links">

</div>