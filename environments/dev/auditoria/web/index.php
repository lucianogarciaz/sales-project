<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
                require(__DIR__ . '/../../common/config/main.php'), require(__DIR__ . '/../../common/config/main-local.php'), require(__DIR__ . '/../config/main.php'), require(__DIR__ . '/../config/main-local.php')
);

if (!Yii::$app->user->isGuest)
{
    $config['components']['db'] = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=192.168.2.18;port=3307;dbname=sga_aud',
        'username' => 'vanesa',
        'password' => 'pal50vane..',
        'charset' => 'utf8',
    ];
    $config['components']['dbLogin'] = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=192.168.2.13;port=3307;dbname=sga_dev',
        'username' => 'root',
        'password' => 'Pal2014',
        'charset' => 'utf8',
    ];
}

$application = new yii\web\Application($config);
$application->run();
