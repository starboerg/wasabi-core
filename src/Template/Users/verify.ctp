<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */

$this->Html->setTitle(__d('wasabi_core', 'Verify Email'));
$this->Html->setSubTitle($user->username);
?>

<?= $this->Form->create($user) ?>
<p><?= __d('wasabi_core', 'Do you really want to verify the email address of user <strong>{0}</strong>?', $user->username) ?></p>
<?= $this->Form->button(__d('wasabi_core', 'Verify'), ['class' => 'button']) ?>
<?= $this->Form->end() ?>
