<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_history}}`.
 */

class m260104_094829_create_story_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_history}}', [
            'id' => $this->primaryKey(),
            'age' => $this->integer()->notNull(),
            'language' => $this->string(10)->notNull(),
            'characters' => $this->json()->notNull(),
            'content' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->createIndex(
            'idx-story_history-created_at',
            '{{%story_history}}',
            'created_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story_history}}');
    }
}