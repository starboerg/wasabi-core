<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Wasabi\Core\Wasabi;

$identityField = Wasabi::setting('Auth.identityField');
$identityLabel = __d('wasabi_core', 'IDENTITY_FORM_FIELD_LABEL');

$passwordField = Wasabi::setting('Auth.passwordField');
$passwordLabel = __d('wasabi_core', 'Password');

echo $this->Form->control($identityField, [
    'label' => $identityLabel . ':',
    'templates' => 'Wasabi/Core.FormTemplates/default'
]);
echo $this->Form->control($passwordField, [
    'label' => $passwordLabel . ':',
    'templates' => 'Wasabi/Core.FormTemplates/default'
]);
