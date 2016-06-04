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
    echo $this->Form->input('instance_name', [
        'label' => __d('wasabi_core', 'Instance Name'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The name of your CMS instance.')
        ]
    ]);
    echo $this->Form->input('html_title_suffix', [
        'label' => __d('wasabi_core', 'Html Title Suffix'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The Html title suffix of your CMS instance.')
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Login Message'),
        'description' => __d('wasabi_core', 'An optional message that is displayed on top of the login page.')
    ]);
    echo $this->Form->input('Login__Message__show', [
        'label' => __d('wasabi_core', 'Display Login Message?'),
        'options' => [
            '0' => __d('wasabi_core', 'No'),
            '1' => __d('wasabi_core', 'Yes')
        ]
    ]);
    echo $this->Form->input('Login__Message__text', [
        'label' => __d('wasabi_core', 'Login Message Text'),
        'templateVars' => [
            'info' =>
                __d('wasabi_core', 'The text of the login message.') . '<br>' .
                __d('wasabi_core', 'Allowed Html tags: &lt;b&gt;&lt;strong&gt;&lt;a&gt;&lt;br&gt;&lt;br/&gt;')
        ],
        'type' => 'textarea',
        'rows' => 2
    ]);
    echo $this->Form->input('Login__Message__class', [
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
    echo $this->Form->input('Login__HeartBeat__max_login_time', [
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
    echo $this->Form->input('Email__email_sender_name', [
        'label' => __d('wasabi_core', 'Email Sender Name'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The name used as sender for all backend emails.')
        ]
    ]);
    echo $this->Form->input('Email__email_sender', [
        'label' => __d('wasabi_core', 'Email Sender Address'),
        'templateVars' => [
            'info' => __d('wasabi_core', 'The email address used as sender for all backend emails.')
        ]
    ]);
    echo $this->Html->div('form-controls');
        echo $this->Form->button(__d('wasabi_core', 'Save'), ['div' => false, 'class' => 'button']);
        echo $this->Guardian->protectedLink(__d('wasabi_core', 'Cancel'), [
            'plugin' => 'Wasabi/Core',
            'controller' => 'Settings',
            'action' => 'general'
        ]);
    echo $this->Html->tag('/div');
echo $this->Form->end();
