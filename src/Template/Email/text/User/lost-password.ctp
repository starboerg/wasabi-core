<?php
/**
 * @var Wasabi\Core\View\AppView $this
 * @var string $username
 * @var array $resetPasswordLink
 */

$this->set('title', __d('wasabi_core', 'Password Reset'));

echo __d(
    'dialog',
    'Hello {username},{nl}you recently requested a password reset. To change your password click the following link.{nl}{nl}{resetPasswordLink}',
    [
        'username' => $username,
        'nl' => "\n",
        'resetPasswordLink' => $this->Url->build($resetPasswordLink, true) . "\n"
    ]
);
