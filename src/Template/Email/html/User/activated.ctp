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
        'username' => '<b>' . $user->username . '</b>',
        'nl' => '<br>',
        'loginLink' => $this->Email->bigActionButton(
            __d('wasabi_core', 'Login'),
            $this->Route->login()
        )
    ]
);
