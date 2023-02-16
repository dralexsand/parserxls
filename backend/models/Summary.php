<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "summary".
 *
 * @property int $id
 * @property int $product_id
 * @property int $month_id
 * @property float|null $cost
 */
class Summary extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'month_id'], 'required'],
            [['product_id', 'month_id'], 'default', 'value' => null],
            [['product_id', 'month_id'], 'integer'],
            [['cost'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'month_id' => 'Month ID',
            'cost' => 'Cost',
        ];
    }

}
