<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        :root {
            --bs-primary: #6f42c1;
            --bs-btn-primary-bg: #6f42c1;
            --bs-btn-primary-border-color: #6f42c1;
            --bs-link-color: #6f42c1;
            --bs-link-hover-color: #59359a;
        }
        .navbar-custom {
            background-color: #6f42c1 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-primary:hover {
            background-color: #59359a;
            border-color: #59359a;
        }
        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="d-flex flex-column h-100 bg-light">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => '🦄 Testter AI Story',
        'brandUrl' => ['/story/default/index'],
        'options' => ['class' => 'navbar-expand-md navbar-dark navbar-custom fixed-top'],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => [
            ['label' => 'Генератор', 'url' => ['/story/default/index']],
            ['label' => 'История', 'url' => ['/story/default/history']],
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container pt-5 mt-4">
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="footer mt-auto py-3 text-muted">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; Testter.kz <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end">AI Powered Generation</div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>