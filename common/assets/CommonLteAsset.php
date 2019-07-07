<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CommonLteAsset extends AssetBundle
{
    public $sourcePath = '@common';
    public $css = [
        'css/main.css',
        'css/custom.css',
            /* 'css/pace.css',
              'css/select2.css',
              'css/jquery-sortable.css', */
    ];
    public $js = [
        'scripts/Main.js',
        'scripts/VueDirectives.js',
            /*    'scripts/Sortable.js',
              'scripts/Notificaciones.js', */
    ];
    public $publishOptions = [
        'forceCopy' => true,
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\BowerAsset',
            //'yii\widgets\MaskedInputAsset',
    ];
}
