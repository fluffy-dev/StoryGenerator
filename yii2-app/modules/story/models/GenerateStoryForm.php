<?php

namespace app\modules\story\models;

use yii\base\Model;

/**
 * Form model for story generation parameters.
 */
class GenerateStoryForm extends Model
{
    public $age;
    public $language;
    public $characters;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['age', 'language', 'characters'], 'required'],
            ['age', 'integer', 'min' => 1, 'max' => 16],
            ['language', 'in', 'range' => ['ru', 'kk']],
            ['characters', 'each', 'rule' => ['string', 'min' => 2]],
            // Ensure at least one character is selected/entered (logic handled in view mostly)
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