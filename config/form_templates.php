<?php
return [
    'formStart' => '<form {{attrs}} novalidate>',
    'label' => '<label {{attrs}}>{{text}}{{labelInfo}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
    'inputContainer' => '<div class="form-row form-row-{{type}}{{required}}">{{content}}</div>',
    'inputContainerError' => '<div class="form-row form-row-{{type}}{{required}} error">{{content}}</div>',
    'formGroup' => '{{label}}<div class="field">{{input}}{{info}}{{error}}</div>',
    'section' => '<div class="form-section"><div class="form-section-title">{{title}}</div><div class="form-section-description">{{description}}</div></div>',
    'info' => '<div class="form-info"><div class="form-info-content{{class}}">{{text}}</div></div>',
    'checkboxFormGroup' => '<label{{formRowFor}}>{{formRowLabel}}{{formRowLabelInfo}}</label><div class="field">{{label}}{{formRowInfo}}</div>',
    'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
    'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'toggleSwitchFormGroup' => '<label{{formRowFor}}>{{formRowLabel}}{{formRowLabelInfo}}</label><div class="field">{{input}}{{formRowInfo}}</div>',
    'toggleSwitch' => '{{checkbox}}<span><a></a><span>{{onLabel}}</span><span>{{offLabel}}</span></span>'
];
