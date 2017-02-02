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
