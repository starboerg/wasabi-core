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
    'Wasabi' => [
        'Auth' => [
            'identity_field' => 'email',
            'identity_label' => __d('wasabi_core', 'Email'),
            'password_field' => 'password',
            'password_label' => __d('wasabi_core', 'Password')
        ]
    ]
];
