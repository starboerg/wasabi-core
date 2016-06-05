<?php
/**
 * Wasabi CMS
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Controller\Component;

use Cake\Controller\Component\FlashComponent as CakeFlashComponent;
use Cake\Network\Exception\InternalErrorException;

/**
 * Class FlashComponent
 *
 * @method void error() error(string $message, string $key = 'flash', boolean $dismiss = true)
 * @method void info() info(string $message, string $key = 'flash', boolean $dismiss = true)
 * @method void success() success(string $message, string $key = 'flash', boolean $dismiss = true)
 * @method void warning() warning(string $message, string $key = 'flash', boolean $dismiss = true)
 */
class FlashComponent extends CakeFlashComponent
{
    /**
     * {@inheritDoc}
     */
    public function __call($name, $args)
    {
        if (count($args) < 1) {
            throw new InternalErrorException('Flash message missing.');
        }

        $message = $args[0];
        $key = (isset($args[1]) && !empty($args[1])) ? $args[1] : 'flash';
        $dismiss = isset($args[2]) ? $args[2] : true;
        $params = [
            'type' => $name,
            'plugin' => 'Wasabi/Core'
        ];

        if ($dismiss === false) {
            $params['class'] = 'flash-message--no-dismiss';
        }

        $this->set($message, [
            'key' => $key,
            'element' => 'Wasabi/Core.default',
            'params' => $params
        ]);
    }
}
