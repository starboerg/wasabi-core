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
namespace Wasabi\Core\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Wasabi\Core\Navigation\Menu;

class MenuListener implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Wasabi.Backend.Menu.initMain' => [
                'callable' => 'initBackendMenuMainItems',
                'priority' => 1000
            ]
        ];
    }

    /**
     * Initialize the backend main menu items.
     *
     * @param Event $event An event instance.
     * @return void
     * @throws \Aura\Intl\Exception
     */
    public function initBackendMenuMainItems(Event $event)
    {
        /** @var Menu $menu the "backend.main" Nav instance */
        $menu = $event->getSubject();

        $menu
            ->addMenuItem([
                'alias' => 'dashboard',
                'name' => __d('wasabi_core', 'Dashboard'),
                'priority' => 1,
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Dashboard',
                    'action' => 'index'
                ],
                'icon' => 'icon-dashboard'
            ])
            ->addMenuItem([
                'alias' => 'content',
                'name' => __d('wasabi_core', 'Content'),
                'priority' => 1000,
                'icon' => 'icon-content',
            ])
            ->addMenuItem([
                'alias' => 'media',
                'name' => __d('wasabi_core', 'Media'),
                'priority' => 3000,
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Media',
                    'action' => 'index'
                ],
                'icon' => 'icon-image',
            ])
            ->addMenuItem([
                'alias' => 'administration',
                'name' => __d('wasabi_core', 'Administration'),
                'priority' => 4000,
                'icon' => 'icon-administration'
            ])
            ->addMenuItem([
                'alias' => 'users',
                'name' => __d('wasabi_core', 'Users'),
                'priority' => 1000,
                'parent' => 'administration',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Users',
                    'action' => 'index'
                ],
                'doNotMatchAction' => 'profile'
            ])
            ->addMenuItem([
                'alias' => 'groups',
                'name' => __d('wasabi_core', 'Groups'),
                'priority' => 2000,
                'parent' => 'administration',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Groups',
                    'action' => 'index'
                ]
            ])
            ->addMenuItem([
                'alias' => 'languages',
                'name' => __d('wasabi_core', 'Languages'),
                'priority' => 3000,
                'parent' => 'administration',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Languages',
                    'action' => 'index'
                ]
            ])
            ->addMenuItem([
                'alias' => 'permissions',
                'name' => __d('wasabi_core', 'Permissions'),
                'priority' => 4000,
                'parent' => 'administration',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Permissions',
                    'action' => 'index'
                ]
            ])
            ->addMenuItem([
                'alias' => 'settings',
                'name' => __d('wasabi_core', 'Settings'),
                'priority' => 5000,
                'icon' => 'icon-settings'
            ])
            ->addMenuItem([
                'alias' => 'settings_general',
                'name' => __d('wasabi_core', 'General'),
                'priority' => 1000,
                'parent' => 'settings',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Settings',
                    'action' => 'general'
                ],
                'matchAction' => true
            ])
            ->addMenuItem([
                'alias' => 'settings_cache',
                'name' => __d('wasabi_core', 'Cache'),
                'priority' => 2000,
                'parent' => 'settings',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Settings',
                    'action' => 'cache'
                ],
                'matchAction' => true
            ])
            ->addMenuItem([
                'alias' => 'settings_media',
                'name' => __d('wasabi_core', 'Media'),
                'priority' => 3000,
                'parent' => 'settings',
                'url' => [
                    'plugin' => 'Wasabi/Core',
                    'controller' => 'Settings',
                    'action' => 'media'
                ],
                'matchAction' => true
            ]);
    }
}
