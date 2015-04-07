<?php
/**
 * Wasabi Core Guardian Event Listener
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
namespace Wasabi\Core\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Wasabi\Core\Controller\Component\GuardianComponent;
use Wasabi\Core\Config;

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
                'priority' => Config::$priority
            ]
        ];
    }

    /**
     * Adds all core wasabi guest actions that do not need a logged in user.
     *
     * @param Event $event
     */
    public function getGuestActions(Event $event)
    {
        /** @var GuardianComponent $guardian */
        $guardian = $event->subject();

        $guardian->addGuestActions([
            'Wasabi/Core.Users.login',
            'Wasabi/Core.Users.logout',
            'Wasabi/Core.Users.register',
            'Wasabi/Core.Users.unauthorized'
        ]);
    }
}
