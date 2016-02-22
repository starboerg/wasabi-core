<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */
?>
<?= $this->Form->input('username', ['label' => __d('wasabi_core', 'Username') . ':', 'templates' => 'Wasabi/Core.form_templates_default']) ?>
<?= $this->Form->input('password', ['label' => __d('wasabi_core', 'Password') . ':', 'templates' => 'Wasabi/Core.form_templates_default']) ?>
