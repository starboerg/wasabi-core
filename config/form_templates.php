<?php
return [
    'formStart' => '<form {{attrs}} novalidate>',
    'label' => '<label {{attrs}}>{{text}}{{info}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>{{info}}',
    'inputContainer' => '<div class="form-row form-row-{{type}}{{required}}">{{content}}</div>',
    'inputContainerError' => '<div class="form-row form-row-{{type}}{{required}} error">{{content}}</div>',
    'formGroup' => '{{label}}<div class="field">{{input}}{{error}}</div>',
    'section' => '<div class="form-section"><div class="form-section-title">{{title}}</div><div class="form-section-description">{{description}}</div></div>',
    'info' => '<div class="form-info"><div class="form-info-content{{class}}">{{text}}</div></div>',
    'checkboxFormGroup' => '<label>{{formRowLabel}}{{formRowLabel}}</label><div class="field">{{label}}{{formRowInfo}}</div>',
    'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>{{info}}',
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>{{info}}',
    'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>{{info}}'
];
