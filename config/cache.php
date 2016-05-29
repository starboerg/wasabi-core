<?php
return [
    'Cache' => [
        'wasabi/core/longterm' => [
            'className' => 'File',
            'duration' => '+999 days',
            'prefix' => '',
            'path' => CACHE . 'wasabi' . DS . 'core' . DS . 'longterm'
        ],
        'wasabi/core/guardian_paths' => [
            'className' => 'File',
            'duration' => '+999 days',
            'prefix' => '',
            'path' => CACHE . 'wasabi' . DS . 'core' . DS . 'guardian_paths'
        ],
        'wasabi/core/group_permissions' => [
            'className' => 'File',
            'duration' => '+999 days',
            'prefix' => '',
            'path' => CACHE . 'wasabi' . DS . 'core' . DS . 'group_permissions'
        ],
        'wasabi/core/routes' => [
            'className' => 'File',
            'duration' => '+999 days',
            'prefix' => '',
            'path' => CACHE . 'wasabi' . DS . 'core' . DS . 'routes'
        ]
    ]
];
