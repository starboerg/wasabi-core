<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Wasabi\Core\Wasabi;

$identityField = Wasabi::setting('Auth.identity_field');
$identityLabel = __d('wasabi_core', 'Email');

$passwordField = Wasabi::setting('Auth.password_field');
$passwordLabel = __d('wasabi_core', 'Password');

echo $this->Form->input($identityField, [
    'label' => $identityLabel . ':',
    'templates' => 'Wasabi/Core.FormTemplates/default'
]);
echo $this->Form->input($passwordField, [
    'label' => $passwordLabel . ':',
    'templates' => 'Wasabi/Core.FormTemplates/default'
]);
