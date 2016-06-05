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
namespace Wasabi\Core\Controller;

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Wasabi\Core\Model\Table\SettingsTable;

/**
 * Class AppController
 *
 * @property \Wasabi\Core\Controller\Component\FlashComponent $Flash
 */
class AppController extends Controller
{
    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param Event $event An Event instance.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->_loadSettings();
    }

    /**
     * Loads all settings from db and triggers the event 'Settings.afterLoad'
     * that can be listened to by plugins to further modify the settings.
     *
     * Structure:
     * ----------
     * Array(
     *     'PluginName|ScopeName' => Array(
     *         'key1' => 'value1',
     *         ...
     *     ),
     *     ...
     * )
     *
     * Access via:
     * -----------
     * Configure::read('Settings.ScopeName.key1');
     *
     * @return array
     */
    protected function _loadSettings()
    {
        $settings = Cache::remember('settings', function () {
            /** @var SettingsTable $Settings */
            $Settings = $this->loadModel('Wasabi/Core.Settings');
            return $Settings->getAllKeyValues();
        }, 'wasabi/core/longterm');

        $event = new Event('Settings.afterLoad', $settings);
        $this->eventManager()->dispatch($event);

        if ($event->result !== null) {
            $settings = $event->result;
        }

        Configure::write('Settings', $settings);
    }
}
