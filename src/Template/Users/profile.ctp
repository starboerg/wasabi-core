<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var array $languages
 */

use Wasabi\Core\Wasabi;

$this->Html->setTitle(__d('wasabi_core', 'Edit Profile'));
$this->Html->setSubTitle($user->fullName());

echo $this->Form->create($user, ['class' => 'no-top-section']);
    echo $this->Form->input('id', ['type' => 'hidden']);
    if (Wasabi::setting('Core.User.has_username')) {
        echo $this->Form->input('username', [
            'label' => __d('wasabi_core', 'Username'),
            'disabled' => 'disabled'
        ]);
    }
    echo $this->Form->input('email', [
        'label' => __d('wasabi_core', 'Email'),
        'templateVars' => [
            'info' => '<span class="email-verified">' . __d('wasabi_core', 'You verified your email address <strong>{0}</strong> on <strong>{1}</strong>.', $user->email, $user->verified_at->format('d. M Y, H:i')) . '</span>'
        ]
    ]);
    if (Wasabi::setting('Core.User.has_firstname_lastname')) {
        echo $this->Form->input('firstname', [
            'label' => __d('wasabi_core', 'First Name')
        ]);
        echo $this->Form->input('lastname', [
            'label' => __d('wasabi_core', 'Last Name')
        ]);
    }
    echo $this->Form->input('title', [
        'label' => __d('wasabi_core', 'Title')
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Change Password'),
        'description' => __d('wasabi_core', 'To change your password fill in both password fields. Otherwise leave those fields empty.')
    ]);
    echo $this->Form->input('password', [
        'label' => __d('wasabi_core', 'Password'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The password should consist of 6 to 50 characters. All numbers, letters and special characters are allowed.')
        ],
        'autocomplete' => 'off',
        'value' => ''
    ]);
    echo $this->Form->input('password_confirmation', [
        'label' => __d('wasabi_core', 'Password Confirmation'),
        'type' => 'password',
        'autocomplete' => 'off'
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Language and Time'),
        'description' => __d('wasabi_core', 'Adjust these settings for your current location.')
    ]);
    echo $this->Form->input('language_id', [
        'label' => __d('wasabi_core', 'Backend Interface Language'),
        'options' => $languages,
        'empty' => __d('wasabi_core', 'Please choose...')
    ]);
    if (Wasabi::setting('Core.User.allow_timezone_change')) {
        echo $this->Form->timeZoneSelect('timezone', [
            'label' => __d('wasabi_core', 'Timezone')
        ]);
    }

    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
        echo $this->Guardian->protectedLink(
            __d('wasabi_core', 'Cancel'),
            $this->request->referer(true)
        );
    echo $this->Html->tag('/div');
echo $this->Form->end();
