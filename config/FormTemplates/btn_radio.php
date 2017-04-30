<?php
return [
    'label' => '<label {{attrs}}>{{text}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
    'inputContainer' => '{{content}}',
    'inputContainerError' => '{{content}}',
    'formGroup' => '{{label}}{{input}}{{error}}',
    'checkboxFormGroup' => '<label>{{formRowLabel}}</label><div class="field">{{label}}</div>',
    'nestingLabel' => '{{hidden}}<label class="btn btn-primary"{{attrs}}>{{input}}{{text}}</label>'
];
