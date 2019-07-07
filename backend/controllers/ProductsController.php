<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\GestorProducts;
use backend\models\Products;

class ProductsController extends Controller
{
    public function actionIndex()
    {
        $gestor = new GestorProducts();
        $products = $gestor->ListProducts();
        return $this->render('index', [
            'products' => $products,
            'title' => 'List of products'
        ]);
    }
    public function actionDisable($IdProduct)
    {
        $product = new Products();
        Yii::$app->response->format = 'json';
        if (intval($IdProduct)) {
            $product->IdProduct = $IdProduct;
        } else {
            return ['error' => 'The transaction is invalid.'];
        }
        $return = $product->Disable();
        
        if ($return['Id'] > 0) {
            return ['error' => null];
        } else {
            return ['error' => $return['Message']];
        }
    }
    public function actionNew()
    {
        $product = new Products();
        $product->setScenario(Products::SCENARIO_NEW);
        if ($product->load(Yii::$app->request->post()) && $product->validate()) {
            $gestor = new GestorProducts();
            $result = $gestor->NewProduct($product);

            Yii::$app->response->format = 'json';

            if ($result['Id'] > 0) {
                return ['error' => null];
            } else {
                return ['error' => $result['Message']];
            }
        } else {
            return $this->renderAjax('new', [
                        'title' => 'New product',
                        'model' => $product
            ]);
        }
    }
}
