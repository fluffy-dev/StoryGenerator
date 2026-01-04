<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\story\models\GenerateStoryForm */

$this->title = 'Генератор сказок';

// Registering Markdown parser JS if needed or using backend parsing.
// For streaming, we will use a simple frontend markdown parser like 'marked' from CDN for real-time preview.
$this->registerJsFile('https://cdn.jsdelivr.net/npm/marked/marked.min.js');
?>

<div class="story-generator-index">
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-top: 4px solid #6f42c1 !important;">
                <div class="card-body">
                    <h4 class="card-title text-primary" style="color: #6f42c1 !important;">Параметры сказки</h4>
                    <hr>

                    <?php $form = ActiveForm::begin([
                        'id' => 'story-form',
                        'action' => ['generate'],
                        'options' => ['class' => 'needs-validation'],
                    ]); ?>

                    <?= $form->field($model, 'age')->input('number', ['min' => 1, 'max' => 99]) ?>

                    <?= $form->field($model, 'language')->dropDownList([
                        'ru' => 'Русский',
                        'kk' => 'Қазақша',
                    ]) ?>

                    <?= $form->field($model, 'characters')->checkboxList([
                        'Алдар Көсе' => 'Алдар Көсе',
                        'Бауырсақ' => 'Бауырсақ / Колобок',
                        'Қанбақ шал' => 'Қанбақ шал',
                        'Тазша бала' => 'Тазша бала',
                        'Ер Төстік' => 'Ер Төстік',
                        'Арыстан' => 'Арыстан / Лев',
                        'Заяц' => 'Қоян / Заяц',
                        'Лиса' => 'Түлкі / Лиса'
                    ]) ?>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('✨ Сгенерировать сказку', [
                            'class' => 'btn btn-block btn-lg text-white w-100',
                            'style' => 'background-color: #6f42c1;',
                            'id' => 'generate-btn'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <div class="mt-3 text-center">
                <?= Html::a('📖 История сказок', ['history'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div id="loader" style="display: none; text-align: center; padding: 50px;">
                        <div class="spinner-border" style="color: #6f42c1;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">ИИ пишет сказку...</p>
                    </div>

                    <div id="result-container" class="markdown-body" style="min-height: 300px;">
                        <div class="text-center text-muted mt-5">
                            <h3 style="opacity: 0.3;">✨</h3>
                            <p>Здесь появится ваша сказка</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<< JS
$('#story-form').on('beforeSubmit', function(e) {
    e.preventDefault();

    var form = $(this);
    var btn = $('#generate-btn');
    var resultContainer = $('#result-container');
    var loader = $('#loader');

    // UI Reset
    btn.prop('disabled', true);
    resultContainer.html('');
    loader.show();

    // Collect data
    var formData = new FormData(form[0]);

    // Use fetch for streaming support
    fetch(form.attr('action'), {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    }).then(response => {
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let markdownText = '';

        loader.hide();

        function read() {
            return reader.read().then(({ done, value }) => {
                if (done) {
                    btn.prop('disabled', false);
                    return;
                }

                const chunk = decoder.decode(value);
                const lines = chunk.split('\\n\\n');

                lines.forEach(line => {
                    if (line.startsWith('data: ')) {
                        const dataStr = line.replace('data: ', '');
                        if (dataStr === '[DONE]') return;

                        try {
                            const data = JSON.parse(dataStr);
                            if (data.text) {
                                markdownText += data.text;
                                resultContainer.html(marked.parse(markdownText));
                                // Auto scroll to bottom
                                // window.scrollTo(0, document.body.scrollHeight);
                            } else if (data.error) {
                                resultContainer.html('<div class="alert alert-danger">' + data.error + '</div>');
                            }
                        } catch (e) {
                            // ignore parsing errors for partial chunks
                        }
                    }
                });

                return read();
            });
        }

        return read();
    }).catch(err => {
        loader.hide();
        btn.prop('disabled', false);
        resultContainer.html('<div class="alert alert-danger">Ошибка сети</div>');
    });

    return false; // Prevent default form submission
});
JS;
$this->registerJs($script);
?>