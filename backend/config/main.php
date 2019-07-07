<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'language' => 'es',
    'sourceLanguage' => 'en',
    'name' => 'Novicap',
    'id' => 'backend',
    'charset' => 'UTF-8',
    'layout' => 'lteadmin',
    'defaultRoute' => 'sales/index',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                    [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => '/sales/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller>/<id:\d+>' => '<controller>',
                '<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                '<controller>/index' => '<controller>',
            ],
        ],
        
    ],
    'params' => $params,
];
