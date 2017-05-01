<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use Wasabi\Core\Wasabi;

echo $this->Form->input(Wasabi::setting('Auth.identity_field'), [
    'label' => Wasabi::setting('Auth.identity_label') . ':',
    'templates' => 'Wasabi/Core.FormTemplates/default'
]);
echo $this->Form->input(Wasabi::setting('Auth.password_field'), [
    'label' => Wasabi::setting('Auth.password_label') . ':',
    'templates' => 'Wasabi/Core.FormTemplates/default'
]);
