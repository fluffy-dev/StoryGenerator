<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'История сказок';
?>
<div class="story-history">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('← Назад к генератору', ['index'], ['class' => 'btn btn-link']) ?></p>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'created_at',
                        'format' => ['datetime', 'php:d.m.Y H:i'],
                        'label' => 'Дата',
                    ],
                    [
                        'attribute' => 'language',
                        'value' => function ($model) {
                            return $model->language === 'ru' ? '🇷🇺 Русский' : '🇰🇿 Қазақша';
                        },
                        'label' => 'Язык',
                    ],
                    [
                        'attribute' => 'age',
                        'label' => 'Возраст',
                    ],
                    [
                        'attribute' => 'characters',
                        'value' => function ($model) {
                            $chars = is_string($model->characters) ? json_decode($model->characters) : $model->characters;
                            return implode(', ', $chars);
                        },
                        'label' => 'Персонажи',
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('Читать', $url, ['class' => 'btn btn-sm btn-primary', 'style' => 'background-color: #6f42c1; border: none;']);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>