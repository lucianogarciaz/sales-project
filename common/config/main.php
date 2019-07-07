<?php

return [
    'timeZone' => 'Europe/Dublin',
    'language' => 'es',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'formatter' => [
            'defaultTimeZone' => 'Europe/Dublin',
            'dateFormat' => 'dd/MM/yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => '',
            'locale' => 'es-AR'
        ],
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
    ],
];
