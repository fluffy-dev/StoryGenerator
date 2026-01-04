<?php

use yii\helpers\Html;
use yii\helpers\Markdown;

/* @var $this yii\web\View */
/* @var $model app\modules\story\models\StoryHistory */

$this->title = 'Сказка #' . $model->id;
?>
<div class="story-view">
    <div class="mb-3">
        <?= Html::a('← К списку', ['history'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background-color: #6f42c1;">
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <strong>Язык:</strong> <?= $model->language === 'ru' ? 'RU' : 'KK' ?> |
                    <strong>Возраст:</strong> <?= $model->age ?>
                </span>
                <small><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
            </div>
        </div>
        <div class="card-body">
            <div class="markdown-body">
                <?= Markdown::process($model->content) ?>
            </div>
        </div>
    </div>
</div>