<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    'formStart' => '<form {{attrs}} novalidate>',
    'label' => '<label {{attrs}}>{{text}}{{labelInfo}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
    'inputContainer' => '<div class="form-row form-row-{{type}}{{required}}{{formRowClass}}">{{content}}</div>',
    'inputContainerError' => '<div class="form-row form-row-{{type}}{{required}}{{formRowClass}} error">{{content}}</div>',
    'formGroup' => '{{label}}<div class="field">{{input}}{{info}}{{error}}</div>',
    'section' => '<div class="form-section{{class}}"{{attrs}}><div class="form-section-title">{{title}}</div><div class="form-section-description">{{description}}</div></div>',
    'info' => '<div class="form-info"><div class="form-info-content{{class}}">{{text}}</div></div>',
    'checkboxFormGroup' => '<label{{formRowFor}}>{{formRowLabel}}{{formRowLabelInfo}}</label><div class="field">{{label}}{{formRowInfo}}</div>',
    'radioWrapper' => '<div class="radio">{{label}}</div>',
    'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
    'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'toggleSwitchFormGroup' => '<label{{formRowFor}}>{{formRowLabel}}{{formRowLabelInfo}}</label><div class="field">{{input}}{{formRowInfo}}</div>',
    'toggleSwitch' => '{{checkbox}}<span><a></a><span>{{onLabel}}</span><span>{{offLabel}}</span></span>'
];
