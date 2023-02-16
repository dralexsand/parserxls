<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%summary}}`.
 */
class m230216_100820_create_summary_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('summary', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'month_id' => $this->integer()->notNull(),
            'cost' => $this->float(2)->null()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('summary');
    }
}
