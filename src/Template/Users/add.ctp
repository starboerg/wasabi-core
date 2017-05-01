<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\User $user
 * @var \Cake\Collection\Collection $groups
 * @var array $languages
 */

use Wasabi\Core\Wasabi;

if ($this->request->params['action'] === 'add') {
    $this->Html->setTitle(__d('wasabi_core', 'Create a new User'));
} else {
    $this->Html->setTitle(__d('wasabi_core', 'Edit User'));
    $this->Html->setSubTitle($user->get('username'));
}

$isEdit = ($this->request->params['action'] === 'edit');

$emailInfo = false;
if ($user->verified && !empty($user->verified_at)) {
    $emailInfo = '<span class="email-verified">' . __d('wasabi_core', 'The user has verified his email address <strong>{0}</strong> on <strong>{1}</strong>.', $user->email, $user->verified_at->format('d. M Y, H:i')) . '</span>';
} elseif ($isEdit) {
    $emailInfo = '<span class="email-not-verified">' . __d('wasabi_core', 'The user has not yet verified his email address.') . '</span>';
}

if (Wasabi::setting('Core.User.belongs_to_many_groups')) {
    $groupField = 'groups._ids';
    $groupOptions = [
        'label' => __d('wasabi_core', 'Groups'),
        'options' => $groups,
        'multiple' => 'checkbox',
        'required' => true
    ];
} else {
    $groupField = 'groups._ids.0';
    if (empty($user->groups)) {
        $value = null;
    } else {
        $value = $user->groups[0]->id;
    }
    $groupOptions = [
        'label' => __d('wasabi_core', 'Group'),
        'type' => 'radio',
        'options' => $groups,
        'required' => true,
        'value' => $value
    ];
}

echo $this->Form->create($user, ['class' => 'no-top-section']);
    if ($isEdit) {
        echo $this->Form->input('id', ['type' => 'hidden']);
    }
    echo $this->Form->input('email', [
        'label' => __d('wasabi_core', 'Email'),
        'templateVars' => [
            'info' => $emailInfo
        ]
    ]);
    if (Wasabi::setting('Core.User.has_username')) {
        echo $this->Form->input('username', [
            'label' => __d('wasabi_core', 'Username')
        ]);
    }
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
    echo $this->Form->input($groupField, $groupOptions);
    if ($isEdit) {
        $inactiveOption = [
            'value' => 0,
            'text' => __d('wasabi_core', 'inactive')
        ];
        if ($user->id === Wasabi::user()->id) {
            $inactiveOption['disabled'] = 'disabled';
        }
        $activeOption = [
            'value' => 1,
            'text' => __d('wasabi_core', 'active')
        ];
        if (!$user->verified) {
            $activeOption['disabled'] = 'disabled';
        }
        echo $this->Form->input('active', [
            'label' => __d('wasabi_core', 'Account Status'),
            'options' => [$inactiveOption, $activeOption],
            'templateVars' => [
                'info' => __d('wasabi_core', 'Please choose the account status for this user. An account activation requires a verfied email address.')
            ]
        ]);
        echo $this->Form->widget('section', [
            'title' => __d('wasabi_core', 'Change Password'),
            'description' => __d('wasabi_core', 'To change the user’s password fill in both password fields. Otherwise leave those fields empty.')
        ]);
        echo $this->Form->input('password', [
            'label' => __d('wasabi_core', 'Password'),
            'templateVars' => [
                'info' => __d('wasabi_core', 'The password should consist of 6 to 50 characters. All numbers, letters and special characters are allowed.')
            ],
            'autocomplete' => 'off'
        ]);
        echo $this->Form->input('password_confirmation', [
            'label' => __d('wasabi_core', 'Password Confirmation'),
            'type' => 'password',
            'autocomplete' => 'off'
        ]);
    }
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Language and Time'),
        'description' => __d('wasabi_core', 'Adjust these settings for the user’s current location.')
    ]);
    echo $this->Form->input('language_id', [
        'label' => __d('wasabi_core', 'Backend Interface Language'),
        'options' => $languages,
        'empty' => __d('wasabi_core', 'Please choose...')
    ]);
    if (Wasabi::setting('Core.User.allow_timezone_change')) {
        echo $this->Form->timeZoneSelect('timezone');
    }
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
        echo $this->Guardian->protectedLink(
            __d('wasabi_core', 'Cancel'),
            $this->Filter->getBacklink($this->Route->usersIndex())
        );
    echo $this->Html->tag('/div');
echo $this->Form->end();
