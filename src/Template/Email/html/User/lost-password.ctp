<?php
/**
 * @var Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var array $resetPasswordLink
 */

$this->set('title', __d('wasabi_core', 'Password Reset'));

echo __d(
    'wasabi_core',
    'Hello {username},{nl}you recently requested a password reset. To change your password click the following link.{nl}{nl}{resetPasswordLink}',
    [
        'username' => '<b>' . $user->username . '</b>',
        'nl' => '<br>',
        'resetPasswordLink' => $this->Email->bigActionButton(__d('wasabi_core', 'Reset password'), $resetPasswordLink)
    ]
);
