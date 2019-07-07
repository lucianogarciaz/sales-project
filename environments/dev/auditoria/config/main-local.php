<?php

$config = [
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '192.168.*', '10.*'],
            'historySize' => 500,
        ],
    ],
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

if (!YII_ENV_TEST)
{
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
}

return $config;
