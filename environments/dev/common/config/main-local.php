<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=DBHOST;dbname=DBNAME;port=PORT',
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
        ],
        'dbmts' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=DBHOSTMTS;dbname=DBNAMEMTS;port=PORTMTS',
            'username' => 'USERMTS',
            'password' => 'PASSMTS',
            'charset' => 'utf8',
        ],
     
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'REDISHOST',
            'port' => 6379,
            'database' => 0,
        ],
        'elephantio' => [
            'class' => 'sammaye\elephantio\ElephantIo',
            'host' => 'NODEHOST:3000',
        ],
    ],
];
