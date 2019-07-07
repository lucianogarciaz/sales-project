<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="site-index">
</div>

<div class="body-content">

    <div class="row">
        <div class="col-lg-4">

        </div>
        <div class="col-lg-4">
            <h1 class="text-center">Welcome to the Novicap Challenge</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5">

        </div>
        <div class="col-lg-4">
            <a type="button" class="btn btn-primary text-center" href="<?= Url::to(['/ventas/index']) ?>"
               data-hint="Nueva Venta">
                <i class="fa fa-plus"></i>Nueva Venta
            </a> 
        </div>
    </div>
</div>

