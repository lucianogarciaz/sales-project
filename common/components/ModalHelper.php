<?php

namespace common\components;

use Yii;

class ModalHelper
{
    public static function agregarModal(string $titulo, string $url)
    {
        Yii::$app->session->setFlash('modal', [
            'titulo' => $titulo,
            'url' => $url,
        ]);
    }

    public static function hayModal(): bool
    {
        return Yii::$app->session->hasFlash('modal');
    }

    public static function dameModal(): array
    {
        return Yii::$app->session->getFlash('modal');
    }
}
