<?php
return [
    'Wasabi' => [
        'Form' => [
            'Templates' => [
                'core' => [
                    'formStart' => '<form {{attrs}}>',
                    'label' => '<label {{attrs}}>{{text}}</label>',
                    'inputContainer' => '<div class="form-row form-row-{{type}}{{required}}">{{content}}</div>',
                    'formGroup' => '{{label}}<div class="field">{{input}}</div>',
                ]
            ]
        ]
    ]
];
