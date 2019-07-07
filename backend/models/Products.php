<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class Products extends Model
{
    public function __construct()
    {
    }

    public $IdProduct;
    public $Product;
    public $Description;
    public $State;
    
    public $Price; //denormalized
    const SCENARIO_NEW = 'new';
    const SCENARIO_EDIT = 'edit';
    
    public function rules()
    {
        return [
            [['IdProduct'], 'integer'],
            [['Price'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*[.]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],

            // new
            [['Product', 'Description', 'Price'], 'required', 'on' => self::SCENARIO_NEW],
            // editar
            [['IdProduct', 'Product', 'Description'], 'required', 'on' => self::SCENARIO_EDIT],
            // Safe
            [['IdProduct', 'Product', 'Description', 'State', 'Price'], 'safe'],
        ];
    }
    /**
     * Returns a product with the currently price
     */
    public function Get()
    {
        $sql = "CALL nsp_get_product(:IdProduct) ";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':IdProduct' => $this->IdProduct,
        ]);

        $this->attributes = $query->queryOne();
    }
    /**
     * Update the state of the product to D (Disabled).
     * @return type
     */
    public function Disable()
    {
        $sql = "CALL nsp_disable_product(:IdProduct) ";

        $query = Yii::$app->db->createCommand($sql);

        $query->bindValues([
            ':IdProduct' => $this->IdProduct,
        ]);

        return $query->queryOne();
    }
}
