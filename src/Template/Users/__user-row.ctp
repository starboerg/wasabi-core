<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var boolean $displayUsername
 * @var boolean $displayFirstnameLastname
 */
use Wasabi\Core\Wasabi;

?><tr>
    <td class="col-id center" data-title="<?= __d('wasabi_core', 'ID') ?>"><?= $user->id ?></td>
    <?php if ($displayUsername): ?>
    <td class="col-username" data-title="<?= __d('wasabi_core', 'Username') ?>">
        <?php
        if (Wasabi::user()->can('edit', $user)) {
            echo $this->Guardian->protectedLink(
                $user->username,
                $this->Route->usersEdit($user->id),
                [
                    'title' => __d('wasabi_core', 'Edit User')
                ],
                true
            );
        } else {
            echo $user->username;
        }
        ?>
    </td>
    <?php endif; ?>
    <?php if ($displayFirstnameLastname): ?>
    <td class="col-name" data-title="<?= __d('wasabi_core', 'Name') ?>">
        <?php
        if ($displayUsername) {
            echo $user->fullName(true);
        } else {
            if (Wasabi::user()->can('edit', $user)) {
                echo $this->Guardian->protectedLink(
                    $user->fullName(true),
                    $this->Route->usersEdit($user->id),
                    [
                        'title' => __d('wasabi_core', 'Edit User')
                    ],
                    true
                );
            } else {
                echo $user->fullName(true);
            }
        }
        ?>
    </td>
    <?php endif; ?>
    <td class="col-email" data-title="<?= __d('wasabi_core', 'Email') ?>">
        <?php
        if (!$displayUsername && !$displayFirstnameLastname
            && Wasabi::user()->can('edit', $user)
        ) {
            echo $this->Guardian->protectedLink(
                $user->email,
                $this->Route->usersEdit($user->id),
                [
                    'title' => __d('wasabi_core', 'Edit User')
                ],
                true
            );
        } else {
            echo $user->email;
        }
        ?>
    </td>
    <td class="col-groups" data-title="<?= __d('wasabi_core', 'Groups') ?>">
        <?php
        if (!empty($user->groups)) {
            echo join('<br>', $user->getGroupNames());
        } else {
            echo '---';
        }
        ?>
    </td>
    <td class="col-status" data-title="<?= __d('wasabi_core', 'Status') ?>"><?php
        if ($user->verified === false) {
            if (Wasabi::user()->can('verify', $user)) {
                echo $this->Guardian->protectedConfirmationLink(
                    '<span class="label">' . __d('wasabi_core', 'not verified') . '</span>',
                    $this->Route->usersVerify($user->id),
                    [
                        'title' => __d('wasabi_core', 'Manually verify the user\'s email address.'),
                        'confirm-title' => __d('wasabi_core', 'Verify Email'),
                        'confirm-message' => __d('wasabi_core', 'Do you really want to verify the email address of user <strong>{0}</strong>?', $user->fullName()),
                        'escape' => false
                    ],
                    true
                );
            } else {
                echo '<span class="label">' . __d('wasabi_core', 'not verified') . '</span>';
            }
        } else {
            echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'The user has verified his email address.') . '">' . __d('wasabi_core', 'verified') . '</span>';
        }
        echo ' ';
        if ($user->active === false) {
            if (Wasabi::user()->can('activate', $user)) {
                echo $this->Guardian->protectedConfirmationLink(
                    '<span class="label">' . __d('wasabi_core', 'inactive') . '</span>',
                    $this->Route->usersActivate($user->id),
                    [
                        'title' => __d('wasabi_core', 'Activate user &quot;{0}&quot;', $user->fullName()),
                        'confirm-title' => __d('wasabi_core', 'Activate User'),
                        'confirm-message' => __d('wasabi_core', 'Activate user <strong>{0}</strong>?', $user->fullName()),
                        'escape' => false
                    ],
                    true
                );
            } else {
                echo '<span class="label" title="' . __d('wasabi_core', 'The user account is inactive.') . '">' . __d('wasabi_core', 'inactive') . '</span>';
            }
        } else {
            if (Wasabi::user()->can('deactivate', $user)) {
                echo $this->Guardian->protectedConfirmationLink(
                    '<span class="label label--success">' . __d('wasabi_core', 'active') . '</span>',
                    $this->Route->usersDeactivate($user->id),
                    [
                        'title' => __d('wasabi_core', 'Deactivate user &quot;{0}&quot;', $user->fullName()),
                        'confirm-title' => __d('wasabi_core', 'Deactivate User'),
                        'confirm-message' => __d('wasabi_core', 'Deactivate user <strong>{0}</strong>?', $user->fullName()),
                        'escape' => false
                    ],
                    true
                );
            } else {
                echo '<span class="label label--success cursor--help" title="' . __d('wasabi_core', 'The user account is active.') . '">' . __d('wasabi_core', 'active') . '</span>';
            }
        }
    ?></td>
    <td class="col-actions center" data-title="<?= __d('wasabi_core', 'Actions') ?>"><?php
        echo $this->Guardian->protectedLink(
            $this->Icon->edit(),
            $this->Route->usersEdit($user->id),
            [
                'title' => __d('wasabi_core', 'Edit User'),
                'escapeTitle' => false
            ]
        );
        if (Wasabi::user()->can('delete', $user)) {
            echo $this->Guardian->protectedConfirmationLink(
                $this->Icon->delete(),
                $this->Route->usersDelete($user->id),
                [
                    'title' => __d('wasabi_core', 'Delete User'),
                    'confirm-title' => __d('wasabi_core', 'Confirm Deletion'),
                    'confirm-message' => __d('wasabi_core', 'Do you really want to delete user <strong>{0}</strong>?', $user->fullName()),
                    'escape' => false
                ]
            );
        }
    ?></td>
</tr>
