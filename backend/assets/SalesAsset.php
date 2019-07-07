<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\assets;

use yii\web\AssetBundle;

class SalesAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'scripts/Sales.js',
    ];
    public $depends = [
        'backend\assets\AppAsset'
    ];
}
