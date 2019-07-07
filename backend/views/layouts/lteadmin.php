<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
$this->registerJs('jQuery(document).ready(function () {Main.init()})', \yii\web\View::POS_END);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?= Html::csrfMetaTags() ?>
        <title><?= Yii::$app->name ?> | <?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="<?= YII_ENV_PROD ? 'skin-black' : 'skin-black' ?> collapsed-sidebar">
        <?php $this->beginBody() ?>
        <div class="wrapper">

            <header class="main-header">
                <a href="<?= Url::home(); ?>" class="logo">
                    <!-- Add the class icon to your logo image or logo icon to add the margining -->
<!--                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/logo_empresa.jpg"/>-->
                </a>

                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">

                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="dropdown user user-menu">
                                
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header bg-light-blue">
                                        <p>
                                        </p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>

            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <?= $this->render('menu') ?>

                </section>
                <!-- /.sidebar -->
            </aside>


            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?= $this->title ?>
                    </h1>
                    <?=
                    Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'tag' => 'ol'
                    ])
                    ?>
                </section>
                <section id="top" class="content">
                    <?= $content ?>
                </section>
                <!--</aside>-->

            </div>
            <!--            <footer class="main-footer">
                            <span data-toggle="snackbar" data-content=""></span>
                        </footer>-->

            <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
