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
        'info' => __d('wasabi_core', 'The name of your CMS instance.')
    ]);
    echo $this->Form->input('html_title_suffix', [
        'label' => __d('wasabi_core', 'Html Title Suffix'),
        'info' => __d('wasabi_core', 'The Html title suffix of your CMS instance.')
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
        'labelInfo' => __d('wasabi_core', 'The text of the login message.'),
        'info' => __d('wasabi_core', 'allowed Html tags: &lt;b&gt;&lt;strong&gt;&lt;a&gt;&lt;br&gt;&lt;br/&gt;'),
        'type' => 'textarea',
        'rows' => 2
    ]);
    echo $this->Form->input('Login__Message__class', [
        'label' => __d('wasabi_core', 'Login Message Class'),
        'label_info' => __d('wasabi_core', 'The CSS class that is applied to the message box.'),
        'options' => [
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'error'
        ]
    ]);
    echo $this->Form->widget('section', [
        'title' => __d('wasabi_core', 'Email'),
        'description' => __d('wasabi_core', 'Configure how backend emails are sent to users.')
    ]);
    echo $this->Form->input('Email__email_sender', [
        'label' => __d('wasabi_core', 'Email Sender'),
        'info' => __d('wasabi_core', 'The email address used as sender for all backend emails.')
    ]);
    echo $this->Form->input('Email__Activation__subject', [
        'label' => __d('wasabi_core', 'Activation Email Subject'),
        'info' => __d('wasabi_core', 'The subject used for activation emails.')
    ]);
    echo $this->Form->input('Email__Verification__subject_admin', [
        'label' => __d('wasabi_core', '[Admin] Verification Email Subject'),
        'info' => __d('wasabi_core', 'The subject used for verification emails, whenever a user’s email address is verified manually by an admin.')
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
