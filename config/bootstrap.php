<?php
/**
 * Wasabi Core Plugin bootstrap
 *
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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\I18n\I18n;
use Cake\I18n\MessagesFileLoader;
use Cake\Routing\DispatcherFactory;
use Wasabi\Core\Event\GuardianListener;
use Wasabi\Core\Event\LanguagesListener;
use Wasabi\Core\Event\MenuListener;
use Wasabi\Core\Controller\Component\GuardianComponent;

try {
    // Load and apply the Wasabi Core cache config.
    Configure::load('Wasabi/Core.cache', 'default');
    foreach (Configure::consume('Cache') as $key => $config) {
        Cache::config($key, $config);
    }
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}

// Load messages from src/Locale/folder/sub_folder/filename.po

//I18n::translator(
//    'wasabi_core',
//    null,
//    new MessagesFileLoader('wasabi_core', 'folder/sub_folder', 'po')
//);

EventManager::instance()->on(new GuardianListener());
EventManager::instance()->on(new MenuListener());
EventManager::instance()->on(new LanguagesListener());

if (!function_exists('guardian')) {
    /**
     * Provides easy access to the GuardianComponent and can be used
     * throughout the whole backend.
     *
     * @return GuardianComponent
     */
    function guardian() {
        return GuardianComponent::getInstance();
    }
}

if (Configure::read('debug')) {
    DispatcherFactory::add('Wasabi/Core.Asset');
}
