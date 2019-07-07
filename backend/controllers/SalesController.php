<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\GestorProducts;
use backend\models\Products;

class SalesController extends Controller
{
    public function behaviors()
    {
        return [
            
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'title' => 'Sales'
        ]);
    }
    public function actionListProducts()
    {
        Yii::$app->response->format = 'json';
        $gestor = new GestorProducts();
        $products = $gestor->ListProducts();
        return $products;
    }
    public function actionGetProduct($IdProduct)
    {
        Yii::$app->response->format = 'json';
        $product = new Products();
        $product->IdProduct = $IdProduct;
        $product->Get();
        return $product;
    }
    public function actionGetDiscounts()
    {
        Yii::$app->response->format = 'json';
        $Sale = json_decode(json_encode(Yii::$app->request->post('Sale')));

        $gestor = new GestorProducts();
        $return = $gestor->CalculateDiscounts($Sale);
        return $return;
    }
    public function actionError()
    {
        return $this->render('/site/error');
    }
}
