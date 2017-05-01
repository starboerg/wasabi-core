<?php
/**
 * @var Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var string $instanceName
 */

$this->set('title', __d('wasabi_core', 'Account Activated'));

echo __d(
    'wasabi_core',
    'Hello {username},{nl}your account has been activated.{nl}{nl}You can now login.{nl}{nl}{loginLink}',
    [
        'username' => $user->username,
        'nl' => "\n",
        'loginLink' => 'Login using the following url:' . "\n" . $this->Email->Url->build($this->Route->login(), true) . "\n"
    ]
);
