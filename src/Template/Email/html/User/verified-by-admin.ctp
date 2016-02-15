<?php
/**
 * @var Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var string $instanceName
 */

$this->set('title', __d('wasabi_core', 'Email Verified'));

echo __d(
    'wasabi_core',
    'Hello {username},{nl}your email address has been manually verified by an admin.{nl}{nl}Your account is not active yet.{nl}You will be notified via email as soon as your account has been activated.',
    [
        'username' => '<b>' . $user->username . '</b>',
        'nl' => '<br>'
    ]
);
