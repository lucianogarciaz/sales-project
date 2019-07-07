<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BowerAsset extends AssetBundle
{
    public $sourcePath = '@bower/';
    public $css = [
        'admin-lte/dist/css/AdminLTE.min.css',
        'font-awesome/css/font-awesome.min.css',
     //   'toastr/toastr.min.css',
    ];
    public $js = [
        'admin-lte/plugins/pace/pace.js',
        'admin-lte/dist/js/app.min.js',
        'bootstrap-confirmation2/bootstrap-confirmation.min.js',

      //  'jquery-sortable/source/js/jquery-sortable.js',
        'vue/dist/vue.js',
      //  'toastr/toastr.min.js',
      //  'lodash/lodash.min.js',
      //  'socket.io-client/dist/socket.io.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
      //  'kartik\select2\Select2Asset',
      //  'kartik\select2\ThemeDefaultAsset',
    ];

    public function init()
    {
        parent::init();

        $lteSkin = YII_ENV_PROD ? 'admin-lte/dist/css/skins/skin-black.min.css' : 'admin-lte/dist/css/skins/skin-black.min.css';

        $this->css[] = $lteSkin;
    }
}
