<?php

namespace app\modules\story\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_history".
 *
 * @property int $id
 * @property int $age
 * @property string $language
 * @property array $characters
 * @property string $content
 * @property string $created_at
 */
class StoryHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['age', 'language', 'characters', 'content'], 'required'],
            [['age'], 'integer'],
            [['characters'], 'safe'], // JSON stored as array via Yii2 logic usually needs handling, simple safe here
            [['content'], 'string'],
            [['language'], 'string', 'max' => 10],
        ];
    }
}