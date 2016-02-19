<?php
/**
 * @var Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var string $instanceName
 */

$this->set('title', __d('wasabi_core', 'Account Locked'));

echo __d(
    'wasabi_core',
    'Hello {username},{nl}your account has been locked.',
    [
        'username' => $user->username,
        'nl' => "\n"
    ]
);
