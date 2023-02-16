<?php

namespace app\services;

use app\models\Product;
use yii\db\DataReader;
use yii\db\Exception;
use yii\db\Query;

class RetriveService
{
    /**
     * @param int $categoryId
     * @return array
     * @throws Exception
     */
    public function getProducts(int $categoryId): array
    {
        $products = Product::find()
            ->select(['id', 'name'])
            ->where(['category_id' => $categoryId])
            ->all();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'name' => $product->name,
                'costs' => $this->getCosts($product->id)
            ];
        }

        return $data;
    }

    /**
     * @param int $productId
     * @return DataReader|array
     * @throws Exception
     */
    public function getCosts(int $productId): \yii\db\DataReader|array
    {
        $query = new Query();

        $query->select
        (
            [
                'month.name AS month',
                'summary.cost'
            ]
        )
            ->from('summary')
            ->join('LEFT JOIN', 'month', 'month.id = summary.month_id')
            ->where('summary.product_id=:id', ['id' => $productId]);

        $command = $query->createCommand();

        return $command->queryAll();
    }
}