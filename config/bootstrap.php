<?php
/**
 * bootstrap
 *
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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\EventManager;
use Cake\Filesystem\Folder;
use Cake\Routing\DispatcherFactory;
use Wasabi\Core\Event\AuthListener;
use Wasabi\Core\Event\DashboardListener;
use Wasabi\Core\Event\GuardianListener;
use Wasabi\Core\Event\LanguagesListener;
use Wasabi\Core\Event\MenuListener;
use Wasabi\Core\Controller\Component\GuardianComponent;
use Wasabi\Core\Event\PolicyListener;

try {
    // Load Wasabi Core config.
    Configure::load('Wasabi/Core.config', 'default');

    // Load and apply the Wasabi Core cache config.
    Configure::load('Wasabi/Core.cache', 'default');
    foreach (Configure::consume('Cache') as $key => $config) {
        if (in_array($key, ['_cake_core_', '_cake_model_'])) {
            continue;
        }
        if (isset($config['path'])) {
            new Folder($config['path'], true, 0775);
        }
        Cache::config($key, $config);
    }
    unset($key, $config);
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}

// Configure plugin translation paths.
Configure::write('App.paths.locales', array_merge((Configure::read('App.paths.locales') ?? []), [Plugin::path('Wasabi/Core') . 'src' . DS . 'Locale' . DS]));

EventManager::instance()->on(new GuardianListener);
EventManager::instance()->on(new AuthListener);
EventManager::instance()->on(new MenuListener);
EventManager::instance()->on(new LanguagesListener);
EventManager::instance()->on(new PolicyListener);
EventManager::instance()->on(new DashboardListener);

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

Plugin::load('FrankFoerster/Filter');

DispatcherFactory::add('FrankFoerster/Asset.Asset');
