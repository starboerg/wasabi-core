<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Model\Validation;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Http\Client;
use Cake\Routing\Router;

/**
 * Class GoogleRecaptchaValidationProvider
 */
class GoogleRecaptchaValidationProvider
{
    /**
     * Validate a google recaptcha.
     *
     * @param string $value The captcha value.
     * @param array $context The form context.
     * @return bool
     */
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
