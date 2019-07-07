<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="error-page">
	<h2 class="headline text-yellow"> <?= $exception->statusCode; ?></h2>
    <div class="error-content">
      <h3><i class="fa fa-warning text-yellow"></i> <?= nl2br(Html::encode($message)) ?></h3>
      
      <jumbotron>
          <h1>      <p>
          Contactese con el administrador del sistema.
          
      </p></h1>
      </jumbotron>
<!--      <p>
        El error ocurrió mientras el servidor web procesaba su respuesta.
        Por favor contactese con su administrador de base de datos si piensa que 
        esto puede ser un error del servidor. 
        Muchas Gracias-->
      <!--</p>-->
</div><!-- /.error-content -->
</div><!-- /.error-page -->
