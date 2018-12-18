<?php
/**
 * @var \Wasabi\Core\View\AppView $this
 * @var \Wasabi\Core\Model\Entity\GeneralSetting $settings
 */

$this->Html->setTitle(__d('wasabi_core', 'General Settings'));
echo $this->Form->create($settings, ['context' => ['table' => 'Wasabi/Core.GeneralSettings']]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Instance'),
        'description' => __d('wasabi_core', 'General instance settings')
    ]);
    echo $this->Form->control('instance_name', [
        'label' => __d('wasabi_core', 'Instance Name'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The name of your app.')
        ]
    ]);
    echo $this->Form->control('instance_short_name', [
        'label' => __d('wasabi_core', 'Instance Short Name'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The short name of your app (preferably only 1 character).')
        ]
    ]);
    echo $this->Form->control('html_title_suffix', [
        'label' => __d('wasabi_core', 'Html Title Suffix'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The Html title suffix of your CMS instance.')
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Login Message'),
        'description' => __d('wasabi_core', 'An optional message that is displayed on top of the login page.')
    ]);
    echo $this->Form->control('Login__Message__show', [
        'label' => __d('wasabi_core', 'Display Login Message?'),
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Form->control('Login__Message__text', [
        'label' => __d('wasabi_core', 'Login Message Text'),
        'templateVars' => [
            'info' =>
                __d('wasabi_core', 'The text of the login message.') . '<br>' .
                __d('wasabi_core', 'Allowed Html tags: &lt;b&gt;&lt;strong&gt;&lt;a&gt;&lt;br&gt;&lt;br/&gt;')
        ],
        'type' => 'textarea',
        'rows' => 2
    ]);
    echo $this->Form->control('Login__Message__class', [
        'label' => __d('wasabi_core', 'Login Message Class'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The CSS class that is applied to the message box.')
        ],
        'options' => [
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'error'
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Session Configuration'),
        'description' => __d('wasabi_core', 'Control the duration of a user’s login session.')
    ]);
    echo $this->Form->control('Login__HeartBeat__max_login_time', [
        'label' => __d('wasabi_core', 'Maximum Login Time'),
        'options' => [
            '900000' => __d('wasabi_core', '15 minutes'),
            '1800000' => __d('wasabi_core', '30 minutes'),
            '2700000' => __d('wasabi_core', '45 minutes'),
            '3600000' => __d('wasabi_core', '1 hour'),
            '6400000' => __d('wasabi_core', '2 hours')
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Email Configuration'),
        'description' => __d('wasabi_core', 'Configure how backend emails are sent to users.')
    ]);
    echo $this->Form->control('Email__email_sender_name', [
        'label' => __d('wasabi_core', 'Email Sender Name'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The name used as sender for all backend emails.')
        ]
    ]);
    echo $this->Form->control('Email__email_sender', [
        'label' => __d('wasabi_core', 'Email Sender Address'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The email address used as sender for all backend emails.')
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Authentication'),
        'description' => __d('wasabi_core', 'Configure the authentication mechanism.')
    ]);
    echo $this->Form->control('Auth__max_login_attempts', [
        'label' => __d('wasabi_core', 'Maximum number of failed login attempts'),
        'default' => 5
    ]);
    echo $this->Form->control('Auth__failed_login_recognition_time', [
        'label' => __d('wasabi_core', 'within x Minutes'),
        'default' => 5,
        'templateVars' => [
            'info' => __d('wasabi_core', 'The number of minutes failed logins are collected.')
        ]
    ]);
    echo $this->Form->control('Auth__block_time', [
        'label' => __d('wasabi_core', 'Block Time'),
        'default' => 30,
        'templateVars' => [
            'info' => __d('wasabi_core', 'If a user continuously tries to login with wrong credentials his IP will be blocked for the entered number of minutes.')
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'User Configuration'),
        'description' => __d('wasabi_core', 'Modify user specific settings.')
    ]);
    echo $this->Form->control('User__has_username', [
        'label' => __d('wasabi_core', 'User has username?'),
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Form->control('User__has_firstname_lastname', [
        'label' => __d('wasabi_core', 'User has firstname and lastname?'),
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Form->control('User__allow_timezone_change', [
        'label' => __d('wasabi_core', 'User may change his timezone?'),
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Form->control('User__belongs_to_many_groups', [
        'label' => __d('wasabi_core', 'User may belong to multiple groups?'),
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Form->control('User__can_register', [
        'label' => __d('wasabi_core', 'User can register?'),
        'default' => '1',
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), ['class' => 'button', 'data-toggle' => 'btn-loading']);
        echo $this->Guardian->protectedLink(
            __d('wasabi_core', 'Cancel'),
            $this->Route->settingsGeneral()
        );
    echo $this->Html->tag('/div');
echo $this->Form->end();
