<?php

namespace Wasabi\Core\Model\Validation;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Http\Client;
use Cake\Routing\Router;

class GoogleRecaptchaValidationProvider
{
    public static function googleRecaptcha($value, $context)
    {
        $httpClient = new Client();
        $googleReponse = $httpClient->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => Configure::read('Google.Recaptcha.secret'),
            'response' => $value,
            'remoteip' => Router::getRequest()->clientIp()
        ]);
        $result = json_decode($googleReponse->body(), true);
        if (!empty($result['error-codes'])) {
            Log::error('Google Recaptcha: ' . $result['error-codes'][0]);
        }
        return (bool)$result['success'];
    }
}