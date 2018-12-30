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

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Wasabi\Core\Controller\Component\GuardianComponent;
use Wasabi\Core\Permission\PermissionGroup;
use Wasabi\Core\Permission\PermissionManager;

class GuardianListener implements EventListenerInterface
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
            'Guardian.getGuestActions' => [
                'callable' => 'getGuestActions',
                'priority' => 1000
            ],
            'Guardian.GroupPermissions.afterSync' => [
                'callable' => 'deleteGuardianPathCache'
            ],
            'Guardian.Permissions.initialize' => [
                'callable' => 'initializePermissions'
            ]
        ];
    }

    /**
     * Adds all core wasabi guest actions that do not need a logged in user.
     *
     * @param Event $event An event instance.
     * @return void
     */
    public function getGuestActions(Event $event)
    {
        /** @var GuardianComponent $guardian */
        $guardian = $event->getSubject();

        $guardian->addGuestActions([
            'Wasabi/Core.Users.login',
            'Wasabi/Core.Users.logout',
            'Wasabi/Core.Authentication.logout',
            'Wasabi/Core.Users.register',
            'Wasabi/Core.Users.unauthorized',
            'Wasabi/Core.Users.lostPassword',
            'Wasabi/Core.Users.resetPassword',
            'Wasabi/Core.Users.requestNewVerificationEmail',
            'Wasabi/Core.Users.verifyByToken',
            'Wasabi/Core.Api/Authentication.login'
        ]);
    }

    /**
     * Delete the guardian paths cache.
     *
     * @param Event $event An event instance.
     * @return void
     */
    public function deleteGuardianPathCache(Event $event)
    {
        Cache::clear(false, 'wasabi/core/guardian_paths');
    }

    /**
     * Initialize all Wasabi/Core permissions.
     *
     * @param Event $event An event instance.
     * @return void
     * @throws \Aura\Intl\Exception
     * @throws \ReflectionException
     */
    public function initializePermissions(Event $event)
    {
        /** @var PermissionManager $pm */
        $pm = $event->getSubject();

        $dashboard = new PermissionGroup();
        $dashboard
            ->setId('dashboard')
            ->setName(__d('wasabi_core', 'Dashboard'))
            ->setPriority(1000)
            ->addPermission(
                $pm->createPermission(1000, 'dashboard.index', __d('wasabi_core', 'Dashboard'), [
                    'Wasabi/Core.Dashboard.index'
                ])
            );

        $users = new PermissionGroup();
        $users
            ->setId('administration.users')
            ->setName(__d('wasabi_core', 'Administration &rsaquo; Users'))
            ->setPriority(2000)
            ->addPermissions([
                $pm->createPermission(1000, 'users.index', __d('wasabi_core', 'Overview'), [
                    'Wasabi/Core.Users.index'
                ]),
                $pm->createPermission(2000, 'users.add', __d('wasabi_core', 'Add'), [
                    'Wasabi/Core.Users.add'
                ]),
                $pm->createPermission(3000, 'users.edit', __d('wasabi_core', 'Edit'), [
                    'Wasabi/Core.Users.edit'
                ]),
                $pm->createPermission(4000, 'users.delete', __d('wasabi_core', 'Delete'), [
                    'Wasabi/Core.Users.delete'
                ]),
                $pm->createPermission(5000, 'users.verify', __d('wasabi_core', 'Verify email address (via backend)'), [
                    'Wasabi/Core.Users.verify'
                ]),
                $pm->createPermission(6000, 'users.activate', __d('wasabi_core', 'Activate'), [
                    'Wasabi/Core.Users.activate'
                ]),
                $pm->createPermission(7000, 'users.deactivate', __d('wasabi_core', 'Deactivate'), [
                    'Wasabi/Core.Users.deactivate'
                ]),
                $pm->createPermission(8000, 'users.profile', __d('wasabi_core', 'Profile'), [
                    'Wasabi/Core.Users.profile'
                ])
            ]);

        $groups = new PermissionGroup();
        $groups
            ->setId('administration.groups')
            ->setName(__d('wasabi_core', 'Administration &rsaquo; Groups'))
            ->setPriority(3000)
            ->addPermissions([
                $pm->createPermission(1000, 'groups.index', __d('wasabi_core', 'Overview'), [
                    'Wasabi/Core.Groups.index'
                ]),
                $pm->createPermission(2000, 'groups.add', __d('wasabi_core', 'Add'), [
                    'Wasabi/Core.Groups.add'
                ]),
                $pm->createPermission(3000, 'groups.edit', __d('wasabi_core', 'Edit'), [
                    'Wasabi/Core.Groups.edit'
                ]),
                $pm->createPermission(4000, 'groups.delete', __d('wasabi_core', 'Delete'), [
                    'Wasabi/Core.Groups.delete'
                ])
            ]);

        $languages = new PermissionGroup();
        $languages
            ->setId('administration.languages')
            ->setName(__d('wasabi_core', 'Administration &rsaquo; Languages'))
            ->setPriority(4000)
            ->addPermissions([
                $pm->createPermission(1000, 'languages.index', __d('wasabi_core', 'Overview'), [
                    'Wasabi/Core.Languages.index'
                ]),
                $pm->createPermission(2000, 'languages.add', __d('wasabi_core', 'Add'), [
                    'Wasabi/Core.Languages.add'
                ]),
                $pm->createPermission(3000, 'languages.edit', __d('wasabi_core', 'Edit'), [
                    'Wasabi/Core.Languages.edit'
                ]),
                $pm->createPermission(4000, 'languages.delete', __d('wasabi_core', 'Delete'), [
                    'Wasabi/Core.Languages.delete'
                ]),
                $pm->createPermission(6000, 'languages.change', __d('wasabi_core', 'Change content language'), [
                    'Wasabi/Core.Languages.change'
                ])
            ]);

        $permissions = new PermissionGroup();
        $permissions
            ->setId('administration.permissions')
            ->setName(__d('wasabi_core', 'Administration &rsaquo; Permissions'))
            ->setPriority(5000)
            ->addPermissions([
                $pm->createPermission(1000, 'permissions.index', __d('wasabi_core', 'Overview'), [
                    'Wasabi/Core.Permissions.index'
                ]),
                $pm->createPermission(2000, 'permissions.update', __d('wasabi_core', 'Update'), [
                    'Wasabi/Core.Permissions.update'
                ])
            ]);

        $settings = new PermissionGroup();
        $settings
            ->setId('administration.settings')
            ->setName(__d('wasabi_core', 'Settings'))
            ->setPriority(6000)
            ->addPermissions([
                $pm->createPermission(1000, 'settings.general', __d('wasabi_core', 'General'), [
                    'Wasabi/Core.Settings.general'
                ]),
                $pm->createPermission(2000, 'settings.cache', __d('wasabi_core', 'Cache'), [
                    'Wasabi/Core.Settings.cache'
                ])
            ]);

        $pm->addPermissionGroups([
            $dashboard,
            $users,
            $groups,
            $permissions,
            $languages,
            $settings
        ]);
    }
}
