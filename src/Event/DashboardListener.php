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
use Cake\Routing\Router;
use Wasabi\Core\View\Helper\RouteHelper;

class DashboardListener implements EventListenerInterface
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
            'Dashboard.SummaryBoxes.init' => [
                'callable' => 'initSummaryBoxes',
                'priority' => 1000
            ]
        ];
    }

    /**
     * @param Event $event
     * @return array
     * @throws \Aura\Intl\Exception
     */
    public function initSummaryBoxes(Event $event)
    {
        return [
            'core.users' => [
                'cell' => 'Wasabi/Core.Dashboard::activeUsers',
                'title' => __d('wasabi_core', 'Active Users'),
                'class' => [
                    'summary-box--faded-blue',
                    'summary-box--active-users'
                ],
                'unit' => '#',
                'link' => Router::url(RouteHelper::usersIndexActive()),
                'linkTitle' => __d('wasabi_core', 'Show all active users'),
                'iconClass' => 'material-icon-people',
                'priority' => 100
            ],
            'core.users.awaitingActivation' => [
                'cell' => 'Wasabi/Core.Dashboard::usersAwaitingActivation',
                'title' => __d('wasabi_core', 'Users awaiting activation'),
                'class' => [
                    'summary-box--orange',
                    'summary-box--users-awaiting-activation'
                ],
                'unit' => '#',
                'link' => Router::url(RouteHelper::usersIndexInactive()),
                'linkTitle' => __d('wasabi_core', 'Show all inactive users'),
                'iconClass' => 'material-icon-people_outline',
                'priority' => 200
            ]
        ];
    }
}
