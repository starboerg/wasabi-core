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
            'identityField' => 'email',
            'passwordField' => 'password',
            'userModel' => 'Wasabi/Core.Users',
            'loginAction' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Authentication',
                'action' => 'login',
                'prefix' => false
            ],
            'loginRedirect' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Dashboard',
                'action' => 'index',
                'prefix' => false
            ],
            'unauthorizedRedirect' => [
                'plugin' => 'Wasabi/Core',
                'controller' => 'Users',
                'action' => 'unauthorized',
                'prefix' => false
            ],
            'newAccountsNeedActivationToLogin' => true,
            'newAccountsNeedEmailVerificationToLogin' => true,
            'login' => [
                'view' => 'Wasabi/Core.Authentication/login',
                'layout' => 'Wasabi/Core.support'
            ]
        ]
    ]
];
