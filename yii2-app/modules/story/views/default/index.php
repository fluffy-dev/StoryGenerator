<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\story\models\GenerateStoryForm */

$this->title = 'Генератор сказок';

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

                    <?= $form->field($model, 'age')->input('number', [
                        'min' => 1,
                        'max' => 16,
                        'class' => 'form-control age-input'
                    ]) ?>

                    <?= $form->field($model, 'language')->dropDownList([
                        'ru' => 'Русский',
                        'kk' => 'Қазақша',
                    ]) ?>

                    <div class="form-group field-generatestoryform-characters required">
                        <label class="control-label" for="character-list">Персонажи (Максимум 10)</label>
                        <div id="character-list">
                            <?php
                            $values = $model->characters && is_array($model->characters) ? $model->characters : [''];
                            foreach ($values as $index => $value):
                            ?>
                                <input type="text"
                                       class="form-control mb-2 character-input"
                                       name="GenerateStoryForm[characters][]"
                                       value="<?= Html::encode($value) ?>"
                                       placeholder="Имя персонажа..."
                                       autocomplete="off">
                            <?php endforeach; ?>
                        </div>
                        <div class="invalid-feedback d-block"></div>
                        <small class="text-muted">Введите имя, чтобы добавить следующего. Пустые поля удаляются.</small>
                    </div>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('✨ Сгенерировать сказку', [
                            'class' => 'btn btn-block btn-lg text-white w-100',
                            'style' => 'background-color: #6f42c1;',
                            'id' => 'generate-btn',
                            'disabled' => false
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
$(document).ready(function() {
    const maxInputs = 10;
    const minAge = 1;
    const maxAge = 16;

    const container = $('#character-list');
    const submitBtn = $('#generate-btn');
    const ageInput = $('.age-input');

    /**
     * Validates both characters presence and age limits
     */
    function validateFormState() {
        let hasCharacter = false;
        $('.character-input').each(function() {
            if ($(this).val().trim().length > 0) {
                hasCharacter = true;
                return false;
            }
        });

        let ageValid = false;
        const ageVal = parseInt(ageInput.val());
        if (!isNaN(ageVal) && ageVal >= minAge && ageVal <= maxAge) {
            ageValid = true;
        }

        submitBtn.prop('disabled', !(hasCharacter && ageValid));
    }

    function appendInputIfNeeded() {
        const inputs = $('.character-input');
        const lastInput = inputs.last();

        if (inputs.length < maxInputs && lastInput.val().trim() !== '') {
            const newInput = $('<input>').attr({
                type: 'text',
                name: 'GenerateStoryForm[characters][]',
                class: 'form-control mb-2 character-input',
                placeholder: 'Имя персонажа...',
                autocomplete: 'off'
            });
            newInput.hide().appendTo(container).fadeIn(200);
        }
    }

    // Character Input Events
    container.on('input', '.character-input', function() {
        validateFormState();
        if ($(this).is(':last-child')) {
            appendInputIfNeeded();
        }
    });

    container.on('blur', '.character-input', function() {
        const self = $(this);
        if (self.val().trim() === '' && !self.is(':last-child')) {
            self.fadeOut(200, function() {
                $(this).remove();
                validateFormState();
                appendInputIfNeeded();
            });
        }
    });

    // Age Input Events
    ageInput.on('input change', function() {
        validateFormState();
    });

    // Initial check
    validateFormState();
    appendInputIfNeeded();
});

// Submit Handler
$('#story-form').on('beforeSubmit', function(e) {
    e.preventDefault();

    var form = $(this);
    var btn = $('#generate-btn');
    var resultContainer = $('#result-container');
    var loader = $('#loader');

    btn.prop('disabled', true);
    resultContainer.html('');
    loader.show();

    var formData = new FormData(form[0]);

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
                            } else if (data.error) {
                                resultContainer.html('<div class="alert alert-danger">' + data.error + '</div>');
                            }
                        } catch (e) {
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

    return false;
});
JS;
$this->registerJs($script);
?>