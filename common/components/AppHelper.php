<?php

namespace common\components;

use Yii;
use yii\web\Response;
use common\models\Empresa;
use InvalidArgumentException;
use pctux\recaptcha\InvisibleRecaptchaValidator;

class AppHelper
{
    /**
     * Setea el formato de respuesta como JSON. Encodea automáticamente los datos retornados.
     * Equivalente a \common\components\AppHelper::setJsonResponseFormat();
     */
    public static function setJsonResponseFormat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public static function esRecaptchaValido(): bool
    {
        return InvisibleRecaptchaValidator::validate(Yii::$app->request->post(InvisibleRecaptchaValidator::POST_ELEMENT));
    }

    public static function logoFrontend(): string
    {
        $empresa = new Empresa();
        $fecha = date('m-d');

        // Navidad
        if ('12-08' <= $fecha || $fecha <= '01-06') {
            $empresa->DameParametro('LOGONAVIDAD');
        } else {
            $empresa->DameParametro('LOGO');
        }

        $pathLogo = $empresa->Valor;

        $empresa->DameParametro('URLFRONTEND');

        return $empresa->Valor . $pathLogo;
    }
}
