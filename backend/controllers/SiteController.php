<?php

namespace app\controllers;

use app\models\Category;
use app\services\RetriveService;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{

    /**
     * @return Response
     * @throws Exception
     */
    public function actionIndex(): Response
    {
        $data = [];

        foreach (Category::find()->all() as $category) {
            $data[] = [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'products' => (new RetriveService())->getProducts($category->id)
                ]
            ];
        }

        return $this->asJson(['data' => $data]);
    }


}
