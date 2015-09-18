<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->set('bodyCssClass', ['support', 'lost-password']);
?>
<?php
echo $this->Html->image('/wasabi/core/img/wasabi.png');
echo $this->Form->create($user, ['novalidate' => 'novalidate']);
echo $this->Flash->render();
echo $this->Form->input('email', [
    'label' => __d('wasabi_core', 'Email')
]);
?>
<div class="form-controls">
    <?= $this->Form->button(__d('wasabi_core', 'Send'), ['class' => 'button blue']) ?>
</div>
<?= $this->Form->end() ?>