<?php

namespace common\components;

use Yii;
use common\models\extra\Toastrs;
use common\models\extra\Alerts;

class ToastrHelper extends AlertHelper
{
    const FLASH_KEY = 'toastrs';

    /**
     * Retorna todos los toastrs
     *
     * @return Toastrs[]
     */
    public static function listar(): array
    {
        return (array) Yii::$app->session->getFlash(self::FLASH_KEY);
    }
    
    /**
     * Agrega un alert de tipo info (color azul)
     *
     * @param string $mensaje
     * @param string $onClick
     */
    public static function agregarInfo(string $mensaje)
    {
        self::agregar(Alerts::INFO, $mensaje);
    }

    /**
     * Agrega un alert de tipo success (color verde)
     *
     * @param string $mensaje
     * @param string $onClick
     */
    public static function agregarSuccess(string $mensaje)
    {
        self::agregar(Alerts::SUCCESS, $mensaje);
    }

    /**
     * Agrega un alert de tipo warning (color amarillo)
     *
     * @param string $mensaje
     * @param string $onClick
     */
    public static function agregarWarning(string $mensaje)
    {
        self::agregar(Alerts::WARNING, $mensaje);
    }

    /**
     * Agrega un alert de tipo error (color rojo)
     *
     * @param string $mensaje
     * @param string $onClick
     */
    public static function agregarError(string $mensaje)
    {
        self::agregar(Alerts::ERROR, $mensaje);
    }
    
    private static function agregar(string $tipo, string $mensaje, string $onClick = null)
    {
        $toastr = new Toastrs($tipo, $mensaje, $onClick);
        Yii::$app->session->addFlash(self::FLASH_KEY, $toastr);
    }
}
