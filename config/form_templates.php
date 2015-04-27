<?php
return [
    'formStart' => '<form {{attrs}} novalidate>',
    'label' => '<label {{attrs}}>{{text}}{{info}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>{{info}}',
    'inputContainer' => '<div class="form-row form-row-{{type}}{{required}}">{{content}}</div>',
    'inputContainerError' => '<div class="form-row form-row-{{type}}{{required}} error">{{content}}</div>',
    'formGroup' => '{{label}}<div class="field">{{input}}{{error}}</div>',
    'section' => '<div class="form-section"><div class="form-section-title">{{title}}</div><div class="form-section-description">{{description}}</div></div>'
];
