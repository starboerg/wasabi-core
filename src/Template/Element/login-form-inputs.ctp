<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 */

use \Cake\Core\Configure;

if (Configure::read('Wasabi.Auth.loginWithUsernamePassword')) {
    echo $this->Form->input('username', ['label' => __d('wasabi_core', 'Username') . ':', 'templates' => 'Wasabi/Core.form_templates_default']);
}

if (Configure::read('Wasabi.Auth.loginWithEmailPassword')) {
    echo $this->Form->input('email', ['label' => __d('wasabi_core', 'Email') . ':', 'templates' => 'Wasabi/Core.form_templates_default']);
}
echo $this->Form->input('password', ['label' => __d('wasabi_core', 'Password') . ':', 'templates' => 'Wasabi/Core.form_templates_default']);
