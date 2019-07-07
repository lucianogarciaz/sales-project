<?php

namespace common\components\validators;

use yii\validators\Validator;
use Yii;

/**
 * Validador que reemplaza las comas por puntos. Usados para filtrar el ingreso de números.
 */
class DecimalValidator extends Validator
{
    public function init()
    {
        $this->filter = function ($value) {
            return  str_replace(',', '.', $value);
        };
        parent::init();
    }
}
