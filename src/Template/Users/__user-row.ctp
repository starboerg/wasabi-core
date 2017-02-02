<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 */
use Wasabi\Core\Wasabi;

?><tr>
    <td class="col-id center"><?= $user->id ?></td>
    <?php if (Wasabi::setting('Core.User.has_username')): ?>
    <td class="col-username"><?= $this->Guardian->protectedLink($user->username, [
        'plugin' => 'Wasabi/Core',
        'controller' => 'Users',
        'action' => 'edit',
        'id' => $user->id
    ], ['title' => __d('wasabi_core', 'Edit User')], true); ?></td>
    <?php endif; ?>
    <?php if (Wasabi::setting('Core.User.has_firstname_lastname')): ?>
    <td class="col-name"><?= $this->Guardian->protectedLink($user->fullName(true), [
        'plugin' => 'Wasabi/Core',
        'controller' => 'Users',
        'action' => 'edit',
        'id' => $user->id
    ], ['title' => __d('wasabi_core', 'Edit User')], true); ?></td>
    <?php endif; ?>
    <td class="col-email"><?=
        (!Wasabi::setting('Core.User.has_username') && !Wasabi::setting('Core.User.has_firstname_lastname'))
            ? $this->Guardian->protectedLink($user->email, [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'edit',
                'id' => $user->id
            ], ['title' => __d('wasabi_core', 'Edit User')], true)
            : $user->email
        ?></td>
    <td class="col-groups"><?= !empty($user->groups) ? join('<br>', $user->getGroupNames()) : '---' ?></td>
    <td class="col-status"><?php
        $isCurrentUser = ($this->request->session()->read('Auth.User.id') === $user->id);
        if ($user->verified === false) {
            echo $this->Guardian->protectedConfirmationLink(
                '<span class="label">' . __d('wasabi_core', 'not verified') . '</span>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'verify',
                    $user->id
                ],
                [
                    'title' => __d('wasabi_core', 'Manually verify the user\'s email address.'),
                    'confirm-title' => __d('wasabi_core', 'Verify Email'),
                    'confirm-message' => __d('wasabi_core', 'Do you really want to verify the email address of user <strong>{0}</strong>?', $user->fullName()),
                    'escape' => false
                ],
                true
            );
        } else {
            echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'The user has verified his email address.') . '">' . __d('wasabi_core', 'verified') . '</span>';
        }
        echo ' ';
        if ($user->active === false) {
            echo $this->Guardian->protectedConfirmationLink(
                '<span class="label">' . __d('wasabi_core', 'inactive') . '</span>',
                [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'activate',
                    $user->id
                ],
                [
                    'title' => __d('wasabi_core', 'Activate user &quot;{0}&quot;', $user->fullName()),
                    'confirm-title' => __d('wasabi_core', 'Activate User'),
                    'confirm-message' => __d('wasabi_core', 'Activate user <strong>{0}</strong>?', $user->fullName()),
                    'escape' => false
                ],
                true
            );
        } else {
            if ($isCurrentUser) {
                echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'You cannot deactivate your own account.') . '">' . __d('wasabi_core', 'active') . '</span>';
            } else {
                echo $this->Guardian->protectedConfirmationLink(
                    '<span class="label label--success">' . __d('wasabi_core', 'active') . '</span>',
                    [
                        'plugin' => 'Wasabi/Core',
                        'controller' => 'Users',
                        'action' => 'deactivate',
                        $user->id
                    ],
                    [
                        'title' => __d('wasabi_core', 'Deactivate user &quot;{0}&quot;', $user->fullName()),
                        'confirm-title' => __d('wasabi_core', 'Deactivate User'),
                        'confirm-message' => __d('wasabi_core', 'Deactivate user <strong>{0}</strong>?', $user->fullName()),
                        'escape' => false
                    ],
                    true
                );
            }
        }
    ?></td>
    <td class="col-actions center"><?php
        echo $this->Guardian->protectedLink(
            '<i class="wicon-edit"></i>',
            [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'edit',
                'id' => $user->id
            ],
            [
                'title' => __d('wasabi_core', 'Edit User'),
                'escapeTitle' => false
            ],
        true);
        if (($user->id != Wasabi::user()->id) && $user->id != 1) {
            echo $this->Guardian->protectedConfirmationLink('<i class="wicon-remove"></i>', '/backend/users/delete/' . $user->id, [
                'class' => 'action-delete',
                'title' => __d('wasabi_core', 'Delete User'),
                'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                'confirm-message' => __d('wasabi_core', 'Do you really want to delete user <strong>{0}</strong>?', $user->fullName()),
                'escape' => false
            ]);
        }
    ?></td>
</tr>
