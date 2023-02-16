<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%month}}`.
 */
class m230216_100720_create_month_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%month}}', [
            'id' => $this->integer()->unsigned()->unique(),
            'name' => $this->string(20)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%month}}');
    }
}
