<?php

$config = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=DBAUDHOST;port=PORTAUD;dbname=DBAUD',
            'username' => 'USERAUD',
            'password' => 'PASSAUD',
            'charset' => 'utf8',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'zID1WslArmDLDhsuO1iK',
        ],
    ],
];

return $config;
