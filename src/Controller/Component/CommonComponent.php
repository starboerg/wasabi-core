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
namespace Wasabi\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Class CommonComponent
 *
 * @property Controller Controller
 */
class CommonComponent extends Component
{
    /**
     * startup callback
     *
     * Trim all submitted data.
     *
     * @param Event $event
     * @return void
     */
    public function startup(Event $event)
    {
        /** @var Controller $controller */
        $controller = $event->getSubject();
        if (!empty($controller->request->getData()) && !Configure::read('DataPreparation.notrimRequestData')) {
            $controller->request->data = $this->_trimDeep($controller->request->getData());
        }
        if (!empty($controller->request->query) && !Configure::read('DataPreparation.notrimRequestQuery')) {
            $controller->request->query = $this->_trimDeep($controller->request->query);
        }
        if (!empty($controller->request->params['pass']) && !Configure::read('DataPreparation.notrimRequestParams')) {
            $controller->request->params['pass'] = $this->_trimDeep($controller->request->params['pass']);
        }
    }

    /**
     * Trim recursively
     *
     * @param mixed $value
     * @return array|string
     */
    protected function _trimDeep($value)
    {
        $value = is_array($value) ? array_map([$this, '_trimDeep'], $value) : trim($value);
        return $value;
    }
}
