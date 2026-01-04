<?php

namespace app\modules\story\models;

use yii\base\Model;

/**
 * Form model for story generation parameters.
 */
class GenerateStoryForm extends Model
{
    /**
     * @var int Default age is 7
     */
    public $age = 7;
    public $language;
    public $characters;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['age', 'language'], 'required'],
            ['age', 'integer', 'min' => 1, 'max' => 16], // Limits defined here
            ['language', 'in', 'range' => ['ru', 'kk']],

            ['characters', 'filter', 'filter' => function ($value) {
                return is_array($value) ? array_values(array_filter($value, 'trim')) : [];
            }],

            ['characters', 'required', 'message' => 'Необходимо указать минимум одного персонажа.'],

            ['characters', function ($attribute, $params) {
                if (!is_array($this->$attribute)) {
                    $this->addError($attribute, 'Персонажи должны быть списком.');
                    return;
                }
                $count = count($this->$attribute);
                if ($count < 1) {
                    $this->addError($attribute, 'Необходимо указать минимум одного персонажа.');
                }
                if ($count > 10) {
                    $this->addError($attribute, 'Максимум 10 персонажей.');
                }
            }],

            ['characters', 'each', 'rule' => ['string', 'min' => 2, 'max' => 50]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'age' => 'Возраст ребенка',
            'language' => 'Язык сказки',
            'characters' => 'Персонажи',
        ];
    }
}