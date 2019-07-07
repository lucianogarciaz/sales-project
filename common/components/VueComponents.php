<?php
namespace common\components;

use yii\base\Component;
use Yii;
use yii\web\View;

class VueComponents extends Component
{
    public $folder = 'vue-components';

    private $vista;

    public function init()
    {
        $this->vista = Yii::$app->getView();
        return parent::init();
    }

    public function createVueInstance(string $el)
    {
        $vista = Yii::$app->getView();

        $js = "new Vue({el: '{$el}'})";
        return $vista->registerJs($js);
    }

    public function registerComponent(string $componente)
    {
        $template = $this->renderTemplate($componente);
        $js = $this->agregarTemplate($componente, $template);
        $this->registrarJs($componente, $js);
    }

    private function registrarJS(string $componente, string $js)
    {
        $this->vista->registerJs($js, View::POS_END);
    }

    private function agregarTemplate(string $componente, string $template): string
    {
        $pathJs = $this->pathJs($componente);

        return $this->vista->renderFile($pathJs, [
            'template' => json_encode($template),
        ]);
    }

    private function renderTemplate(string $componente): string
    {
        $path = $this->pathVista($componente);
        return Yii::$app->controller->renderFile($path);
    }

    private function pathVista(string $componente): string
    {
        return "{$this->archivoComponente($componente)}.html";
    }

    private function pathJs(string $componente): string
    {
        return "{$this->archivoComponente($componente)}.js";
    }
    
    private function archivoComponente(string $componente): string
    {
        $partes = explode('/', $componente);
        $archivo = end($partes);
        return "{$this->pathComponente($componente)}/{$archivo}";
    }

    private function pathComponente(string $componente): string
    {
        return "{$this->basePath()}/{$componente}";
    }

    private function basePath(): string
    {
        return Yii::getAlias('@app') . "/{$this->folder}";
    }
}
