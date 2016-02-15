<?php
/**
 * @var Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var string $instanceName
 */

$this->set('title', __d('wasabi_core', 'Account Activation'));

echo __d(
    'wasabi_core',
    'Hello {username},{nl}your account has been activated.{nl}{nl}You can now login and start creating content.{nl}{nl}{loginLink}',
    [
        'username' => $user->username,
        'nl' => "\n",
        'loginLink' => 'Login using the following url:' . "\n" . $this->Email->Url->build([
            'plugin' => 'Wasabi/Core',
            'controller' => 'Users',
            'action' => 'login'
        ], true) . "\n"
    ]
);
