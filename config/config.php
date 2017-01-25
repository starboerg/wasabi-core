<?php
return [
    'Wasabi' => [
        'Auth' => [
            'loginWithUsernamePassword' => true,
            'loginWithEmailPassword' => false,
            'simplePermissions' => true
        ],
        'User' => [
            'hasUsername' => true,
            'hasSalutation' => false,
            'hasFirstnameLastname' => false,
            'belongsToManyGroups' => false,
            'allowTimezoneChange' => true
        ]
    ]
];
