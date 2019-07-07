<?php
use app\assets\AppAsset;

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <?php $form = ActiveForm::begin(['id' => 'products-form',]) ?>
        <div class="modal-body">
            <div id="errores-modal"> </div>
            <?= Html::activeHiddenInput($model, 'IdProduct') ?>

            <?= $form->field($model, 'Product') ?>
            <?= $form->field($model, 'Description') ?>
            <?= $form->field($model, 'Price') ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'rol-button']) ?>  
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
