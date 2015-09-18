<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'reset-password']);
?>
<?php
echo $this->Html->image('/wasabi/core/img/wasabi.png');
echo $this->Form->create($user, ['novalidate' => 'novalidate']);
echo $this->Flash->render();
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
?>
<div class="form-controls">
    <?= $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button blue']) ?>
</div>
<?= $this->Form->end() ?>