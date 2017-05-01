<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->Html->setTitle(__d('wasabi_core', 'Deactivate User Account'));
$this->Html->setSubTitle($user->username);
?>

<?= $this->Form->create($user) ?>
<p><?= __d('wasabi_core', 'Do you really want to deactivate the user account of <strong>{0}</strong>?', $user->username) ?></p>
<?= $this->Form->button(__d('wasabi_core', 'Deactivate'), ['class' => 'button', 'data-toggle' => 'btn-loading']) ?>
<?= $this->Form->end() ?>
