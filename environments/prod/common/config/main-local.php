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
             //backup
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            'useFileTransport' => false,
//            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'smtp.gmail.com',
//                'username' => 'apuestas24@gmail.com',
//                'password' => 'backupMail24..',
//                'port' => '587',
//                'encryption' => 'tls',
//            ],
//            'viewPath' => '@common/mail',
//        ],
       
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
