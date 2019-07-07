<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class GestorProducts extends Model
{
    /**
     * Calculate the final price (with discounts if exists) of the purchase.
     * @param type $Products
     * @return type
     */
    public function CalculateDiscounts($products)
    {
        $sql = "CALL nsp_calculate_price(:Products) ";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':Products' => json_encode($products),
        ]);

        return $query->queryOne();
    }
    /**
     * List every product with state = 'A' (Active).
     */
    public function ListProducts()
    {
        $sql = "CALL nsp_list_products() ";

        $query = Yii::$app->db->createCommand($sql);

        return $query->queryAll();
    }
    /**
     * Add a new product. Product, Description, Price can not be null.
     *  Controls that not exists another product with the same name.
     * @param type $product
     * @return type
     */
    public function NewProduct($product)
    {
        $sql = "CALL nsp_new_product(:product,:description, :price) ";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':product' => $product->Product,
            ':description' => $product->Description,
            ':price' => $product->Price
        ]);

        return $query->queryOne();
    }
}
