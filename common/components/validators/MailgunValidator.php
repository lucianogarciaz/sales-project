<?php

namespace common\components\validators;

use yii\validators\Validator;

class MailgunValidator extends Validator
{
    /*
     * No se realiza validación en el servidor
     */
    public function validateValue($value)
    {
        return null;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        parent::clientValidateAttribute($model, $attribute, $view);
        return <<<JS
var url = 'https://api.mailgun.net/v3/address/validate';
var token = 'pubkey-fa34c39809cdf7d91fb0feba2b27b46a';

var email = value;
        deferred.push($.get(url, {
    'address': email,
    'api_key': token
}).done(function (data) {
    if (!data.is_valid)
    {
        if (data.did_you_mean)
            messages.push('La dirección ingresada no es correcta. Quizás quisiste decir ' + data.did_you_mean);
        else
            messages.push("La dirección de email ingresada no es válida.");
        return;
    }
})
        );
JS;
    }
}
