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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Nks77vcX2g8IDKE9e965',
        ],
    ],
];

if (!YII_ENV_TEST)
{
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
}

return $config;
