<?php

namespace common\components\validators;

use yii\validators\Validator;
use Yii;
use yii\validators\FilterValidator;

/**
 * Validador que filtra una cadena con código HTML dejando sólo las etiquetas habilitadas en
 * el atributo 'tags'.
 */
class StripHtmlTagsValidator extends FilterValidator
{
    public $tags = [
        '<br>',
        '<a>',
        '<h1>',
        '<h2>',
        '<h3>',
        '<h4>',
        '<h5>',
        '<h6>',
        '<i>',
        '<u>',
        '<b>',
        '<blockquote>',
        '<ul>',
        '<ol>',
        '<li>',
    ];

    public function init()
    {
        $this->filter = function ($value) {
            return strip_tags($value, $this->allowableTags());
        };
        
        parent::init();
    }

    private function allowableTags()
    {
        return implode('', $this->tags);
    }
}
